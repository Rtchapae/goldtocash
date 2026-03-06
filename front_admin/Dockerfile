FROM node:20-alpine AS development

RUN apk add --no-cache yarn

WORKDIR /app

COPY package*.json ./
COPY yarn.lock ./

RUN yarn install

COPY . .

EXPOSE 5173

CMD ["npm", "run", "dev"]

FROM node:20-alpine AS build

RUN apk add --no-cache yarn

WORKDIR /app

ENV NODE_ENV=production
ENV VITE_API_BASE_URL=/api/v1

COPY package*.json ./
COPY yarn.lock ./

RUN yarn install

COPY . .

RUN yarn build

FROM nginx:alpine AS production

COPY --from=build /app/dist /usr/share/nginx/html

RUN echo 'server {\
    listen 80;\
    server_name localhost;\
    root /usr/share/nginx/html;\
    index index.html index.htm;\
    gzip on;\
    gzip_vary on;\
    gzip_min_length 1024;\
    gzip_proxied any;\
    gzip_comp_level 6;\
    gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss application/atom+xml image/svg+xml;\
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {\
        expires 1y;\
        add_header Cache-Control "public, immutable";\
        try_files $uri =404;\
    }\
    location / {\
        try_files $uri $uri/ /index.html;\
        add_header Cache-Control "no-cache, no-store, must-revalidate";\
        add_header Pragma "no-cache";\
        add_header Expires "0";\
    }\
    location /health {\
        access_log off;\
        return 200 "healthy\\n";\
        add_header Content-Type text/plain;\
    }\
}' > /etc/nginx/conf.d/default.conf

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
  CMD wget --no-verbose --tries=1 --spider http://localhost/ || exit 1

CMD ["nginx", "-g", "daemon off;"]
