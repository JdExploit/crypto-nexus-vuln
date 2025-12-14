from flask import Flask, render_template, request, redirect, session, jsonify
import database # Importamos el módulo de base de datos
import subprocess
import os
import pickle
import base64
import requests
from functools import wraps

# ================= 1. CONFIGURACIÓN INICIAL =================
app = Flask(__name__)
app.secret_key = 'insecure_key_12345' 
app.config['UPLOAD_FOLDER'] = '/app/uploads'
app.config['COMMENTS_FILE'] = '/app/data/comments.txt'

# ================= 2. DECORADORES Y UTILIDADES =================

def login_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if not session.get('logged_in'):
            return redirect('/login')
        return f(*args, **kwargs)
    return decorated_function

def admin_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if not session.get('logged_in') or not session.get('is_admin'):
            return render_template('access_denied.html'), 403 
        return f(*args, **kwargs)
    return decorated_function

# ================= 3. RUTAS BASE Y AUTENTICACIÓN =================

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        username = request.form.get('username', '')
        password = request.form.get('password', '')
        
        # VULNERABILIDAD: SQL Injection en Login
        # Query vulnerable: ' OR '1'='1'--
        query = f"SELECT * FROM user WHERE username = '{username}' AND password = '{password}'"
        
        try:
            user_data_list = database.unsafe_query(query)
            user = user_data_list[0] if user_data_list else None
            
            if user:
                # user[0]=id, user[1]=username, user[4]=is_admin
                session.clear()
                session['logged_in'] = True
                session['user_id'] = user[0]
                session['username'] = user[1]
                session['is_admin'] = bool(user[4])
                return redirect('/dashboard')
            else:
                return render_template('login.html', error='Invalid credentials or SQLi failed.')
        except Exception as e:
            return render_template('login.html', error=f'Database Error: {str(e)}')
            
    return render_template('login.html')

@app.route('/logout')
def logout():
    session.clear()
    return redirect('/')

@app.route('/dashboard')
@login_required
def dashboard():
    # Usamos la ID de la sesión para obtener los datos
    user_id = session.get('user_id')
    user_data = database.get_user_by_id(user_id)
    
    if user_data:
        user_info = {
            'username': user_data[1],
            'email': user_data[2],
            'balance': user_data[3],
            'ssn': user_data[4],
        }
        return render_template('dashboard.html', user=user_info)
    return redirect('/logout')

# ================= 4. RUTAS DE VULNERABILIDAD =================

# 4.1 SQL Injection (SQLi)
@app.route('/search')
def search():
    query = request.args.get('q', 'alice')
    results = []
    error = None
    
    # VULNERABILIDAD: SQL Injection - Concatena la entrada del usuario directamente.
    query_string = f"SELECT id, username, email, balance, ssn FROM user WHERE username LIKE '%{query}%' OR email LIKE '%{query}%'"
    
    try:
        results_data = database.unsafe_query(query_string) 
        
        if results_data:
            for row in results_data:
                results.append({
                    'id': row[0],
                    'username': row[1],
                    'email': row[2],
                    'balance': f"${row[3]:.2f}",
                    'ssn': row[4]
                })
    except Exception as e:
        error = f'SQL Error: {str(e)}'
        
    return render_template('search.html', query=query, results=results, error=error)

# 4.2 Command Injection (CMDi)
@app.route('/ping')
@login_required
def ping():
    host = request.args.get('host', '127.0.0.1')
    output = None
    error = None

    if host:
        # VULNERABILIDAD: Command Injection - No sanitiza el input 'host'.
        try:
            # shell=True permite la inyección de comandos como ';whoami'
            # 
            output = subprocess.check_output(f"ping -c 2 {host}", shell=True, text=True, stderr=subprocess.STDOUT, timeout=5)
        except subprocess.CalledProcessError as e:
            output = f"Command failed with return code {e.returncode}.\nOutput:\n{e.output}"
        except Exception as e:
            error = f"Execution Error: {str(e)}"
    
    return render_template('ping.html', host=host, output=output, error=error)

@app.route('/exec')
@admin_required
def exec_command():
    # Lógica de Command Injection para el panel de administración
    cmd = request.args.get('cmd', 'echo "No command specified"')
    output = None
    
    try:
        output = subprocess.check_output(cmd, shell=True, text=True, stderr=subprocess.STDOUT, timeout=5)
    except subprocess.CalledProcessError as e:
        output = f"Command failed (Return Code {e.returncode}):\n{e.output}"
    except Exception as e:
        output = f"Execution Error: {str(e)}"
        
    return redirect(f'/admin?output={base64.urlsafe_b64encode(output.encode()).decode()}')


# 4.3 Local File Inclusion / Path Traversal (LFI)
@app.route('/download')
@admin_required 
def download():
    filename = request.args.get('file', 'test.txt')
    
    # VULNERABILIDAD: Path Traversal - No sanitiza 'filename'.
    try:
        # Aquí permitimos el acceso a cualquier archivo del sistema.
        # 
        with open(filename, 'r') as f:
            content = f.read()
            return render_template('lfi_result.html', filename=filename, content=content)
    except FileNotFoundError:
        return render_template('lfi_result.html', filename=filename, error="File not found or access denied (LFI attempt).")
    except Exception as e:
        return render_template('lfi_result.html', filename=filename, error=f"Error accessing file: {str(e)}")

# 4.4 Insecure Direct Object Reference (IDOR)
@app.route('/user/profile/<int:user_id>')
@login_required
def view_user_profile(user_id):
    # VULNERABILIDAD: IDOR - No comprueba si 'user_id' es igual a 'session.user_id'
    user_data = database.get_user_by_id(user_id)
    
    if user_data:
        # user_data: (id, username, email, balance, ssn, is_admin)
        user_info = {
            'id': user_data[0],
            'username': user_data[1],
            'email': user_data[2],
            'balance': f"${user_data[3]:.2f}",
            'ssn': user_data[4], # DATO SENSIBLE EXPUESTO
            'is_admin': bool(user_data[5])
        }
        return render_template('profile.html', profile=user_info)
    return "User not found (IDOR access attempt failed)", 404

# 4.5 Cross-Site Scripting (XSS)
@app.route('/comments')
def view_comments():
    comments = []
    if os.path.exists(app.config['COMMENTS_FILE']):
        with open(app.config['COMMENTS_FILE'], 'r') as f:
            comments = [c.strip() for c in f.readlines()]
            
    # La plantilla 'comments.html' renderiza con {{ comment|safe }} - VULNERABILIDAD (Stored XSS)
    # 
    return render_template('comments.html', comments=comments)

@app.route('/post_comment', methods=['POST'])
def post_comment():
    comment_text = request.form.get('comment')
    if comment_text:
        # VULNERABILIDAD: No sanitiza la entrada antes de almacenarla
        # Aseguramos que el directorio exista para el volumen de datos
        os.makedirs(os.path.dirname(app.config['COMMENTS_FILE']), exist_ok=True)
        with open(app.config['COMMENTS_FILE'], 'a') as f:
            f.write(comment_text + '\n')
    return redirect('/comments')

# 4.6 Cross-Site Request Forgery (CSRF)
@app.route('/transfer', methods=['GET', 'POST'])
@login_required
def transfer_funds():
    message = None
    error = None
    
    if request.method == 'POST':
        # VULNERABILIDAD: CSRF - Ausencia total de token de protección
        to_username = request.form.get('to_account')
        amount = request.form.get('amount')
        
        try:
            amount = float(amount)
            if amount <= 0:
                raise ValueError("Amount must be positive.")
            
            message = f"Transfer of ${amount:.2f} to {to_username} initiated. (Transfer vulnerable to CSRF attack)"
            
        except ValueError as e:
            error = str(e)
        except Exception:
            error = "An unexpected error occurred during transfer."
            
    return render_template('transfer.html', message=message, error=error)

# 4.7 Server-Side Request Forgery (SSRF)
@app.route('/fetch')
@admin_required 
def fetch_url():
    url = request.args.get('url', 'http://example.com')
    output = None
    error = None
    
    # VULNERABILIDAD: SSRF - Usa la URL de entrada sin validación de host.
    try:
        # 
        response = requests.get(url, timeout=5, verify=False)
        output = f"Status: {response.status_code}\n\n{response.text[:1000]}"
    except requests.exceptions.Timeout:
        error = "Request timed out."
    except requests.exceptions.ConnectionError:
        error = f"Connection Error: Could not reach {url}. (Try internal IP: 127.0.0.1)"
    except Exception as e:
        error = f"SSRF Error: {str(e)}"
        
    return render_template('ssrf_result.html', url=url, output=output, error=error)

# 4.8 Insecure Deserialization
@app.route('/import_profile', methods=['GET', 'POST'])
def import_profile():
    if request.method == 'POST':
        profile_data = request.form.get('data', '')
        output = None
        error = None
        
        # VULNERABILIDAD: Insecure Deserialization - Usa pickle.loads en datos de entrada.
        try:
            # 
            profile = pickle.loads(base64.b64decode(profile_data))
            output = f'Profile loaded successfully! Details: {profile}'
        except Exception as e:
            error = f'Deserialization Error: {str(e)}'
        
        return render_template('deserialization.html', output=output, error=error)
        
    return render_template('deserialization.html')

# 4.9 Panel de Administración
@app.route('/admin')
@admin_required
def admin_dashboard():
    # Obtener usuarios usando la función centralizada de la DB
    all_users_data = database.get_all_users()

    # Convertir a una lista de diccionarios
    all_users = [{'id': r[0], 'username': r[1], 'email': r[2], 'balance': r[3], 'ssn': r[4], 'is_admin': bool(r[5])} for r in all_users_data]

    # Recuperar la salida del comando si existe (desde /exec)
    command_output_b64 = request.args.get('output', None)
    command_output = None
    if command_output_b64:
        try:
            command_output = base64.urlsafe_b64decode(command_output_b64).decode()
        except Exception:
            command_output = "Error decoding command output."
            
    return render_template('admin.html', all_users=all_users, command_output=command_output)

# ================= 5. INICIO DE LA APLICACIÓN =================

if __name__ == '__main__':
    # Crear directorio de uploads (para Path Traversal/LFI)
    os.makedirs(app.config['UPLOAD_FOLDER'], exist_ok=True)
    
    # Crear archivo de prueba para path traversal
    test_file_path = os.path.join(app.config['UPLOAD_FOLDER'], 'test.txt')
    with open(test_file_path, 'w') as f:
        f.write('This is a test file for path traversal.\nTry: /download?file=../../../etc/passwd\n')
        
    # Crear archivo de comentarios (XSS persistente)
    if not os.path.exists(app.config['COMMENTS_FILE']):
        os.makedirs(os.path.dirname(app.config['COMMENTS_FILE']), exist_ok=True)
        with open(app.config['COMMENTS_FILE'], 'w') as f:
            f.write('<p>Welcome to the comments!</p>\n')
    
    # Inicializar base de datos
    try:
        database.init_database()
    except Exception as e:
        print(f"FATAL ERROR: Failed to initialize database. Check volume permissions: {e}")
        exit(1)
    
    print("\n[+] Starting Vulnerable Bank App on http://0.0.0.0:8080")
    print("[+] Default credentials: admin/admin123, alice/password123, bob/qwerty")
    app.run(host='0.0.0.0', port=8080, debug=True)
