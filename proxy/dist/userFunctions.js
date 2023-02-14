"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.validation = exports.inscription = exports.checkRole = exports.checkUserInfo = exports.connection = void 0;
const axios_1 = __importDefault(require("axios"));
const types_1 = require("./types");
const connection = (username, password, res) => {
    axios_1.default
        .post(types_1.endPointServiceUserLogin, { username, password }, {
        headers: {
            "Content-Type": "application/json",
        },
    })
        .then((resp) => {
        res.json(resp.data);
    })
        .catch((error) => {
        res.json(error);
    });
};
exports.connection = connection;
const checkUserInfo = (token, res) => {
    axios_1.default
        .get(types_1.endPointServiceUserInfo, {
        headers: {
            Authorization: token,
        },
    })
        .then((resp) => {
        res.json(resp.data);
    })
        .catch((error) => {
        res.status(error.response.status).json(error.response.data);
    });
};
exports.checkUserInfo = checkUserInfo;
const checkRole = (role, token, res) => {
    axios_1.default
        .post(types_1.endPointServiceUserCheckRole, { role }, {
        headers: {
            "Content-Type": "application/json",
            Authorization: token,
        },
    })
        .then((resp) => {
        res.json(resp.data);
    })
        .catch((error) => {
        res.status(error.response.status).json(error.response.data);
    });
};
exports.checkRole = checkRole;
const inscription = (email, fisrtName, lastName, phone, country, res) => {
    axios_1.default
        .post(types_1.endPointServiceUserInscription, { email, fisrtName, lastName, phone, country })
        .then((resp) => {
        res.json(resp.data);
    })
        .catch((error) => {
        res.status(error.response.status).json(error.response.data);
    });
};
exports.inscription = inscription;
const validation = (id, data, token, res) => {
    axios_1.default
        .post(types_1.endPointServiceUserInscriptionValide, { id }, {
        headers: {
            "Content-Type": "application/json",
            Authorization: token,
        },
    })
        .then((response) => {
        res.resolve(response.data);
    })
        .catch((error) => {
        console.log(error);
    });
};
exports.validation = validation;
//# sourceMappingURL=userFunctions.js.map