FROM node:20

WORKDIR /app

COPY package*.json ./
RUN npm install -g npm@11.11.0

RUN npm install --save-dev @angular/cli
RUN npm install --save-dev @angular-devkit/build-angular

COPY . .

CMD ["ng","serve","--host","0.0.0.0","--port","4200"]