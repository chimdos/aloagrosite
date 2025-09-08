<?php
// Carrega o arquivo de configuração principal
require_once 'config.php';
// Carrega a classe do banco de dados
require_once DBAPI; // DBAPI foi definido no config.php
// Carrega o cabeçalho da página (com o menu e o Bootstrap)
include(HEADER_TEMPLATE);
?>
<style>
  .login-container {
    background: #fff;
    padding: 30px 40px;
    border-radius: 20px;
    box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 360px;
    margin: 60px auto;
    box-sizing: border-box;
  }
  h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #222;
    font-weight: 600;
    font-size: 28px;
  }
  label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #444;
  }
  input[type="text"],
  input[type="password"] {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 20px;
    border: 1.5px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
    transition: border-color 0.3s ease;
  }
  input[type="text"]:focus,
  input[type="password"]:focus {
    border-color: #4caf50;
    outline: none;
  }
  button {
    width: 100%;
    padding: 12px;
    background-color: #4caf50;
    border: none;
    color: white;
    font-size: 18px;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  button:hover {
    background-color: #43a047;
  }

  /* Para telas pequenas */
  @media (max-width: 400px) {
    .login-container {
      padding: 25px 20px;
      width: 90%;
      max-width: none;
    }
    h2 {
      font-size: 24px;
    }
    button {
      font-size: 16px;
    }
  }
</style>

<div class="login-container">
  <h2>LOGIN</h2>
  <form>
    <label for="nome">Usuário</label>
    <input type="text" id="nome" name="nome" placeholder="Digite seu usuário" required />

    <label for="senha">Senha</label>
    <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required />

    <button type="submit" class="btn btn-primary">Entrar</button>
  </form>
</div>

<?php
// Carrega o rodapé da página
include(FOOTER_TEMPLATE);
?>