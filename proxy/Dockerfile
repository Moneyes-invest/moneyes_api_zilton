FROM node:16

# Create app directory
WORKDIR /var/www

# Install app dependencies
COPY package*.json ./

RUN npm install
#If you are building your code for production
#RUN npm ci --only=production
COPY . .

RUN ls -al /var/www/dist
RUN whoami
# Bundle app source


EXPOSE 8888


CMD [ "node", "dist/app.js" ]

# RUN npm install pm2 -g
# CMD ["pm2-runtime", "start", "package.json"]