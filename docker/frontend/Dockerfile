FROM node:20

WORKDIR /usr/src/app

RUN npm install -g @angular/cli@17.0.1

COPY package*.json ./

RUN npm install --legacy-peer-deps

COPY . .

EXPOSE 4200

CMD ["ng", "serve", "--host", "0.0.0.0", "--poll", "2000"]
