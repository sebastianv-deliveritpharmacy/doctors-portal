FROM node:lts-alpine

WORKDIR /app

COPY package*.json ./
RUN npm install --legacy-peer-deps || npm install  # handles optional dep bug

COPY . .

EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host"]
