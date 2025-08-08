from flask import Flask, request, jsonify
from Gmail import enviar_email
import mysql.connector

app = Flask(__name__)

def get_email_by_id(id_morador):
    try:
        conn = mysql.connector.connect(
            host='localhost',
            user='root',  # ajuste conforme seu usuário
            password='',  # ajuste conforme sua senha
            database='db_shieldtech'
        )
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT email FROM tb_moradores WHERE id_moradores = %s", (id_morador,))
        result = cursor.fetchone()
        cursor.close()
        conn.close()
        if result and result['email']:
            return result['email']
        else:
            return None
    except Exception as e:
        print("Erro ao buscar email do morador:", e)
        return None

@app.route('/')
def index():
    return 'API de envio de email ativa!'

@app.route('/enviar_email', methods=['POST'])
def enviar_email_api():
    data = request.get_json()
    id_morador = data.get('email')  # Aqui 'email' na verdade é o id_morador vindo do PHP
    descricao = data.get('descricao', '')
    email_destinatario = get_email_by_id(id_morador)
    if not email_destinatario:
        return jsonify({'sucesso': False, 'erro': 'Email não encontrado'})
    sucesso = enviar_email(email_destinatario, descricao)
    return jsonify({'sucesso': sucesso})

if __name__ == '__main__':
    app.run(debug=True)
