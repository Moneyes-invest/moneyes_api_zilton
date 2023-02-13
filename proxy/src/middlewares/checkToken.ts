import { Request, Response } from "express";
import { urlsAcceptedWithoutConnection } from "../types";

export default module.exports = function () {
  return function (req: Request, res: Response, next: () => void) {
    const { url } = req;
    if (!urlsAcceptedWithoutConnection.includes(url)) {
      const token = req.header("Authorization");
      if (!token) {
        res.status(500);
        res.send("Token missing");
        return;
      }
    }
    // Implement the middleware function based on the options object
    next();
  };
};
