"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const axios_1 = __importDefault(require("axios"));
const types_1 = require("../types");
const userFunctions_1 = require("../userFunctions");
const initUrls = (app) => {
    app.get(types_1.urlApiUser, (_, res) => {
        axios_1.default.get(types_1.endPointServiceUserHello)
            .then((resp) => {
            res.json(resp.data);
        })
            .catch((error) => {
            res.status(error.response.status).json(error.response.data);
        });
    });
    app.post(types_1.urlApiUserLogin, (req, res) => {
        const body = req.body;
        (0, userFunctions_1.connection)(body.username, body.password, res);
    });
    app.get(types_1.urlApiUserInfo, (req, res) => {
        const token = req.header("Authorization");
        (0, userFunctions_1.checkUserInfo)(token, res);
    });
    app.get(types_1.urlApiAdminInfo, (req, res) => {
        const token = req.header("Authorization");
        axios_1.default
            .get(types_1.endPointServiceUserAdminInfo, {
            headers: {
                Authorization: req.header("Authorization"),
            },
        })
            .then((resp) => {
            res.json(resp.data);
        })
            .catch((error) => {
            res.status(error.response.status).json(error.response.data);
        });
    });
    app.post(types_1.urlApiCheckRole, (req, res) => {
        const body = req.body;
        const token = req.header("Authorization");
        (0, userFunctions_1.checkRole)(body.role, token, res);
    });
    /**
     * Permet de récupérer tous les utilisateurs en attente de validation
     */
    app.get(types_1.urlApiFutureUser, (req, res) => {
        axios_1.default
            .get(types_1.endPointServiceFutureUsers, {
            headers: {
                Authorization: req.header("Authorization"),
            },
        })
            .then((resp) => {
            res.json(resp.data);
        })
            .catch((error) => {
            res.status(error.response.status).json(error.response.data);
        });
    });
    /**
     * Permet d'inscrire l'utilisateur avec les infos qu'il a mit dans le formulaire
     */
    app.post(types_1.urlApiUserInscription, (req, res) => {
        const body = req.body;
        (0, userFunctions_1.inscription)(body.email, body.firstName, body.lastName, body.phone, body.country, res);
    });
    /**
     * Permet de valider les utilisateurs qui se sont inscrits
     */
    app.post(types_1.urlApiUserInscriptionValide, (req, res) => {
        const id = req.params.id;
        const data = req.body;
        const token = req.header("Authorization");
        (0, userFunctions_1.validation)(id, data, token, res);
    });
    /**
     * Permet de récupérer tous les utilisateurs
     */
    app.get(types_1.urlApiUsersInfo, (req, res) => {
        axios_1.default
            .get(types_1.endPointServiceUsersInfo, {
            headers: {
                Authorization: req.header("Authorization"),
            },
        })
            .then((resp) => {
            res.json(resp.data);
        })
            .catch((error) => {
            res.status(error.response.status).json(error.response.data);
        });
    });
};
exports.default = { initUrls };
//# sourceMappingURL=user.js.map