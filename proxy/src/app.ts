import express from "express";
import bodyParser from "body-parser";

import checkToken from "./middlewares/checkToken";
import userService from "./services/user";

import { urlApi } from "./types";
const app = express();
const cors = require('cors')
const port = 8080;
app.use(cors());
app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());
app.use(checkToken());

app.get(urlApi, (_, res) => {
  res.send("Hello API");
});

app.listen(port, () => {
  return console.log(`Express is listening at http://localhost:${port}`);
});

userService.initUrls(app);
