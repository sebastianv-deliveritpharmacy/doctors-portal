# Stage 1: Build
FROM node:lts-alpine as builder

WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Serve with http-server
FROM node:lts-alpine
RUN npm install -g http-server

WORKDIR /app
COPY --from=builder /app/dist ./dist

EXPOSE 8080
CMD ["http-server", "dist", "-p", "8080"]
