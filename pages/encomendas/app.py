from flask import Flask, request, jsonify
import mysql.connector
from GmailEncomendas import enviar_email_encomenda

app = Flask(__name__)

def get_email_by_nome(nome_morador):
    try:
        conn = mysql.connector.connect(
            host='localhost',
            user='root',  # ajuste conforme seu usuário
            password='',  # ajuste conforme sua senha
            database='db_shieldtech'
        )
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT email FROM tb_moradores WHERE nome = %s", (nome_morador,))
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
    return 'API de envio de email de encomendas ativa!'

@app.route('/enviar_email_encomenda', methods=['POST'])
def enviar_email_encomenda_api():
    data = request.get_json()
    nome_morador = data.get('nome_morador')
    descricao = data.get('descricao', '')
    data_recebimento = data.get('data_recebimento', '')
    email_destinatario = get_email_by_nome(nome_morador)
    if not email_destinatario:
        return jsonify({'sucesso': False, 'erro': 'Email do morador não encontrado'})
    sucesso = enviar_email_encomenda(email_destinatario, nome_morador, descricao, data_recebimento)
    return jsonify({'sucesso': sucesso})

if __name__ == '__main__':
    app.run(debug=True)
