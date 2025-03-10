-- Adicionar colunas thumbnail e youtube_link à tabela de cursos
ALTER TABLE courses ADD COLUMN thumbnail VARCHAR(255) NULL;
ALTER TABLE courses ADD COLUMN youtube_link VARCHAR(255) NULL;
