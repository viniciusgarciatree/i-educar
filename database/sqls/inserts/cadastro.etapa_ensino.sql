DROP TABLE cadastro.etapa_ensino;
CREATE TABLE cadastro.etapa_ensino
(
    codigo integer,
    descricao character varying(200)
)
WITH (
    OIDS = FALSE
);

ALTER TABLE cadastro.etapa_ensino
    OWNER to postgres;

INSERT INTO cadastro.etapa_ensino VALUES (1, '1 - Educação Infantil - Creche (0 a 3 anos)');
INSERT INTO cadastro.etapa_ensino VALUES (2, '2 - Educação Infantil - Pré-escola (4 e 5 anos)');
INSERT INTO cadastro.etapa_ensino VALUES (3, '3 - Educação Infantil - Unificada (0 a 5 anos)');
INSERT INTO cadastro.etapa_ensino VALUES (14, '14 - Ensino Fundamental de 9 anos - 1º Ano');
INSERT INTO cadastro.etapa_ensino VALUES (15, '15 - Ensino Fundamental de 9 anos - 2º Ano');
INSERT INTO cadastro.etapa_ensino VALUES (16, '16 - Ensino Fundamental de 9 anos - 3º Ano');
INSERT INTO cadastro.etapa_ensino VALUES (17, '17 - Ensino Fundamental de 9 anos - 4º Ano');
INSERT INTO cadastro.etapa_ensino VALUES (18, '18 - Ensino Fundamental de 9 anos - 5º Ano');
INSERT INTO cadastro.etapa_ensino VALUES (19, '19 - Ensino Fundamental de 9 anos - 6º Ano');
INSERT INTO cadastro.etapa_ensino VALUES (20, '20 - Ensino Fundamental de 9 anos - 7º Ano');
INSERT INTO cadastro.etapa_ensino VALUES (21, '21 - Ensino Fundamental de 9 anos - 8º Ano');
INSERT INTO cadastro.etapa_ensino VALUES (22, '22 - Ensino Fundamental de 9 anos - Multi');
INSERT INTO cadastro.etapa_ensino VALUES (23, '23 - Ensino Fundamental de 9 anos - Correção de Fluxo');
INSERT INTO cadastro.etapa_ensino VALUES (24, '24 - Ensino Fundamental de 8 e 9 anos - Multi 8 e 9 anos');
INSERT INTO cadastro.etapa_ensino VALUES (25, '25 - Ensino Médio - 1ª Série');
INSERT INTO cadastro.etapa_ensino VALUES (26, '26 - Ensino Médio - 2ª Série');
INSERT INTO cadastro.etapa_ensino VALUES (27, '27 - Ensino Médio - 3ª Série');
INSERT INTO cadastro.etapa_ensino VALUES (28, '28 - Ensino Médio - 4ª Série');
INSERT INTO cadastro.etapa_ensino VALUES (29, '29 - Ensino Médio - Não Seriada');
INSERT INTO cadastro.etapa_ensino VALUES (30, '30 - Curso Técnico Integrado (Ensino Médio Integrado) 1ª Série');
INSERT INTO cadastro.etapa_ensino VALUES (31, '31 - Curso Técnico Integrado (Ensino Médio Integrado) 2ª Série');
INSERT INTO cadastro.etapa_ensino VALUES (32, '32 - Curso Técnico Integrado (Ensino Médio Integrado) 3ª Série');
INSERT INTO cadastro.etapa_ensino VALUES (33, '33 - Curso Técnico Integrado (Ensino Médio Integrado) 4ª Série');
INSERT INTO cadastro.etapa_ensino VALUES (34, '34 - Curso Técnico Integrado (Ensino Médio Integrado) Não Seriada');
INSERT INTO cadastro.etapa_ensino VALUES (35, '35 - Ensino Médio - Normal/Magistério 1ª Série');
INSERT INTO cadastro.etapa_ensino VALUES (36, '36 - Ensino Médio - Normal/Magistério 2ª Série');
INSERT INTO cadastro.etapa_ensino VALUES (37, '37 - Ensino Médio - Normal/Magistério 3ª Série');
INSERT INTO cadastro.etapa_ensino VALUES (38, '38 - Ensino Médio - Normal/Magistério 4ª Série');
INSERT INTO cadastro.etapa_ensino VALUES (39, '39 - Curso Técnico - Concomitante');
INSERT INTO cadastro.etapa_ensino VALUES (40, '40 - Curso Técnico - Subsequente');
INSERT INTO cadastro.etapa_ensino VALUES (41, '41 - Ensino Fundamental de 9 anos - 9º Ano');
INSERT INTO cadastro.etapa_ensino VALUES (69, '69 - EJA - Ensino Fundamental - Anos iniciais');
INSERT INTO cadastro.etapa_ensino VALUES (70, '70 - EJA - Ensino Fundamental - Anos finais');
INSERT INTO cadastro.etapa_ensino VALUES (71, '71 - EJA - Ensino Médio');
INSERT INTO cadastro.etapa_ensino VALUES (72, '72 - EJA - Ensino Fundamental - Anos iniciais e Anos finais');
INSERT INTO cadastro.etapa_ensino VALUES (56, '56 - Educação Infantil e Ensino Fundamental de 9 anos - Multietapa');
INSERT INTO cadastro.etapa_ensino VALUES (73, '73 - Curso FIC integrado na modalidade EJA - Nível Fundamental (EJA integrada à Educação Profissional de Nível Fundamental)');
INSERT INTO cadastro.etapa_ensino VALUES (74, '74 - Curso Técnico Integrado na Modalidade EJA (EJA integrada à Educação Profissional de Nível Médio)');
INSERT INTO cadastro.etapa_ensino VALUES (64, '64 - Curso Técnico Misto');
INSERT INTO cadastro.etapa_ensino VALUES (67, '67 - Curso FIC integrado na modalidade EJA - Nível Médio');
INSERT INTO cadastro.etapa_ensino VALUES (68, '68 - Curso FIC Concomitante');