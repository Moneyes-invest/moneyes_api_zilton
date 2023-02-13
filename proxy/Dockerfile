FROM node:16

# Create app directory
WORKDIR /var/www

# Install app dependencies
COPY package*.json ./

RUN npm install
RUN npm install -g supervisor

#If you are building your code for production
#RUN npm ci --only=production
COPY . .

EXPOSE 8888

CMD [ "npm", "run", "watchserver" ]

# CMD [ "npm", "run", "start" ]

# RUN npm install pm2 -g
# CMD ["pm2-runtime", "start", "package.json"]