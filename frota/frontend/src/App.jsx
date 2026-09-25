import { useState, useEffect } from "react";
import "./App.css";

const ENDERECO = "http://localhost:3000/api/trens";

function App() {
  const [trens, setTrens] = useState([]);
  const [aviso, setAviso] = useState("");

  async function carregarTrens() {
    const resposta = await fetch(ENDERECO);

    if (!resposta.ok) {
      setAviso("Não foi possível consultar o servidor.");
      return;
    }

    const dados = await resposta.json();

    setTrens(dados);
    setAviso(dados.length === 0 ? "Nenhum trem encontrado." : "");
  }

  useEffect(() => {
    carregarTrens();
  }, []);

  return (
    <div className="container">
      <h1>Frota Ferroviária</h1>
      {aviso && <p className="aviso">{aviso}</p>}

      <table>
        <thead>
          <tr>
            <th>Prefixo</th>
            <th>Modelo</th>
            <th>Ano</th>
            <th>Situação</th>
          </tr>
        </thead>
        <tbody>
          {
            trens.map((trem) => (
              <tr key={trem.id}>
                <td>{trem.prefixo}</td>
                <td>{trem.modelo}</td>
                <td>{trem.ano}</td>
                <td>{trem.situacao}</td>
              </tr>
            ))
          }
        </tbody>
      </table>
    </div>
  );
}

export default App;