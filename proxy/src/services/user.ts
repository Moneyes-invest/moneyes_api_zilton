import { Express } from "express";
import axios from "axios";

import {
   endPointServiceUserAdminInfo,
   endPointServiceUserHello,
   endPointServiceFutureUsers,
   endPointServiceUsersInfo,
   urlApiAdminInfo,
   urlApiCheckRole,
   urlApiUser,
   urlApiUserInfo,
   urlApiUserLogin,
   urlApiFutureUser,
   urlApiUserInscription,
   urlApiUserInscriptionValide,
   urlApiUsersInfo
} from "../types";
import { checkRole, checkUserInfo, connection, inscription, validation } from "../userFunctions";

const initUrls = (app: Express) => {
   app.get(urlApiUser, (_, res) => {
      axios.get(endPointServiceUserHello)
      .then((resp) => {
         res.json(resp.data);
      })
      .catch((error) => {
         res.status(error.response.status).json(error.response.data);
      });
   });

   app.post(urlApiUserLogin, (req, res) => {
      const body: { username: string; password: string } = req.body;
      connection(body.username, body.password, res);
   });

   app.get(urlApiUserInfo, (req, res) => {
      const token = req.header("Authorization");
      checkUserInfo(token, res);
   });

   app.get(urlApiAdminInfo, (req, res) => {
       const token = req.header("Authorization");

       axios
         .get(endPointServiceUserAdminInfo, {
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

   app.post(urlApiCheckRole, (req, res) => {
      const body: { role: string } = req.body;
      const token = req.header("Authorization");
      checkRole(body.role, token, res);
   });

   /**
    * Permet de récupérer tous les utilisateurs en attente de validation
    */
   app.get(urlApiFutureUser, (req, res) => {
      axios
      .get(endPointServiceFutureUsers, {
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
   app.post(urlApiUserInscription, (req, res) => {
      const body: { email: string; firstName: string; lastName: string; phone: string; country: string; } = req.body;
      inscription(body.email, body.firstName, body.lastName, body.phone, body.country, res);
   });

   /**
    * Permet de valider les utilisateurs qui se sont inscrits
    */
   app.post(urlApiUserInscriptionValide, (req, res) => {
      const id = req.params.id;
      const data = req.body;
      const token = req.header("Authorization");
      validation(id, data, token, res);
   });

   /**
    * Permet de récupérer tous les utilisateurs
    */
   app.get(urlApiUsersInfo, (req, res) => {
      axios
         .get(endPointServiceUsersInfo, {
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

export default { initUrls };