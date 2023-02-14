"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = __importDefault(require("express"));
const body_parser_1 = __importDefault(require("body-parser"));
const checkToken_1 = __importDefault(require("./middlewares/checkToken"));
const user_1 = __importDefault(require("./services/user"));
const types_1 = require("./types");
const app = (0, express_1.default)();
const cors = require('cors');
const port = 8888;
app.use(cors());
app.use(body_parser_1.default.urlencoded({ extended: false }));
app.use(body_parser_1.default.json());
app.use((0, checkToken_1.default)());
app.get(types_1.urlApi, (_, res) => {
    res.send("Hello API");
});
app.listen(port, () => {
    return console.log(`Express is listening at http://localhost:${port}`);
});
user_1.default.initUrls(app);
//# sourceMappingURL=app.js.map