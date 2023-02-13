import axios from "axios";
import {
    endPointServiceUserCheckRole,
    endPointServiceUserInfo,
    endPointServiceUserInscription,
    endPointServiceUserLogin,
    endPointServiceUserInscriptionValide,

} from "./types";

export const connection = (username: string, password: string, res) => {
    axios
        .post(
            endPointServiceUserLogin,
            {username, password},
            {
                headers: {
                    "Content-Type": "application/json",
                },
            })
        .then((resp) => {
            res.json(resp.data);
        })
        .catch((error) => {
            res.status(error.response.status).json(error.response.data);
        });
};

export const checkUserInfo = (token: string, res) => {
    axios
        .get(endPointServiceUserInfo, {
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

export const checkRole = (role: string, token: string, res) => {
    axios
        .post(
            endPointServiceUserCheckRole,
            {role},
            {
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

export const inscription = (email: string, fisrtName: string, lastName: string, phone: string, country: string, res) => {
    axios
        .post(
            endPointServiceUserInscription,
            {email, fisrtName, lastName, phone, country},
        )
        .then((resp) => {
            res.json(resp.data);
        })
        .catch((error) => {
            res.status(error.response.status).json(error.response.data);
        });
}

export const validation = (id: string, data, token: string, res) => {
    axios
        .post(
            endPointServiceUserInscriptionValide,
            {id},
            {
                headers: {
                    "Content-Type": "application/json",
                    Authorization: token,
                },
            }
        )
        .then((response) => {
            res.resolve(response.data);
        })
        .catch((error) => {
            console.log(error);
        });
}
