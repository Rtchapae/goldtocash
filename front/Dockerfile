FROM node:20-alpine AS development

RUN apk add --no-cache yarn

WORKDIR /app

COPY package*.json ./
COPY yarn.lock ./

RUN yarn install

COPY . .

EXPOSE 5175

CMD ["npm", "run", "dev"]

FROM node:20-alpine AS build

RUN apk add --no-cache yarn

WORKDIR /app

ENV NODE_ENV=production
ENV VITE_API_BASE_URL=/api/v1
ENV VITE_FRONT_URL=http://localhost:8080
ENV VITE_ADMIN_URL=http://localhost:8082

COPY package*.json ./
COPY yarn.lock ./

RUN yarn install --frozen-lockfile

COPY . .

RUN npm run build

FROM node:20-alpine AS production

RUN apk add --no-cache yarn

WORKDIR /app

COPY --from=build /app/dist ./dist
COPY --from=build /app/index.html ./
COPY --from=build /app/server.js ./
COPY --from=build /app/package*.json ./
COPY --from=build /app/yarn.lock ./
COPY --from=build /app/public ./public

ENV NODE_ENV=production
ENV VITE_API_BASE_URL=/api/v1
ENV VITE_FRONT_URL=http://localhost:8080
ENV VITE_ADMIN_URL=http://localhost:8082

COPY --from=build /app/src ./src
COPY --from=build /app/vite.config.js ./

RUN yarn install --frozen-lockfile --production && yarn cache clean

RUN addgroup -g 1001 -S nodejs && \
    adduser -S nodejs -u 1001

RUN chown -R nodejs:nodejs /app
USER nodejs

EXPOSE 5175

CMD ["node", "server.js"]
