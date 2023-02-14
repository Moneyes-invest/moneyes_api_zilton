"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
const types_1 = require("../types");
exports.default = module.exports = function () {
    return function (req, res, next) {
        const { url } = req;
        if (!types_1.urlsAcceptedWithoutConnection.includes(url)) {
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
//# sourceMappingURL=checkToken.js.map