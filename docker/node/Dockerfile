FROM node:20

WORKDIR /var/www/html

# Instala dependências
COPY package*.json ./
RUN npm install

CMD ["npm", "run", "dev"]
