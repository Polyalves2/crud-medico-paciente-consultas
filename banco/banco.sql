CREATE DATABASE sistema_ifpe;

USE sistema_ifpe;

CREATE TABLE Medico (
    id_medico INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    especialidade VARCHAR(100) NOT NULL UNIQUE,
);

CREATE TABLE Paciente (
    id_paciente INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    data_nascimento DATE NOT NULL,
    tipo_sanguineo VARCHAR(3),
);

CREATE TABLE Consulta (
    id_paciente INT,
    data_hora DATETIME,
    id_medico INT,
    Observacoes TEXT,
    PRIMARY KEY (id_paciente, id_medico,data_hora),
    FOREIGN KEY (id_paciente) REFERENCES Paciente(id_paciente),
    FOREIGN KEY (id_medico) REFERENCES Medico(id_medico),
);

-- Criar a tabela de imagens
CREATE TABLE imagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    path VARCHAR(255) NOT NULL
);

-- Adicionar a chave estrangeira na tabela paciente
ALTER TABLE paciente
ADD COLUMN imagem_id INT,
ADD FOREIGN KEY (imagem_id) REFERENCES imagens(id) ON DELETE SET NULL;