"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.urlsAcceptedWithoutConnection = exports.endPointServiceUsersInfo = exports.endPointServiceUserInscriptionValide = exports.endPointServiceUserInscription = exports.endPointServiceFutureUsers = exports.endPointServiceUserCheckRole = exports.endPointServiceUserAdminInfo = exports.endPointServiceUserInfo = exports.endPointServiceUserLogin = exports.endPointServiceUserHello = exports.urlServerServiceUser = exports.urlApiUsersInfo = exports.urlApiUserInscriptionValide = exports.urlApiUserInscription = exports.urlApiFutureUser = exports.urlApiCheckRole = exports.urlApiAdminInfo = exports.urlApiUserInfo = exports.urlApiUserLogin = exports.urlApiUser = exports.urlApi = void 0;
/** Server URL */
exports.urlApi = "/api";
exports.urlApiUser = `${exports.urlApi}/.user`;
exports.urlApiUserLogin = `${exports.urlApiUser}/login`;
exports.urlApiUserInfo = `${exports.urlApiUser}/user`;
exports.urlApiAdminInfo = `${exports.urlApiUser}/admin`;
exports.urlApiCheckRole = `${exports.urlApiUser}/checkRole`;
exports.urlApiFutureUser = `${exports.urlApiUser}/futureUsers`;
exports.urlApiUserInscription = `${exports.urlApiUser}/inscription`;
exports.urlApiUserInscriptionValide = `${exports.urlApiUser}/inscription/valideUser/:id`;
exports.urlApiUsersInfo = `${exports.urlApiUser}/users`;
/** Service User */
exports.urlServerServiceUser = "http://caddy/api";
exports.endPointServiceUserHello = `${exports.urlServerServiceUser}/hello`;
exports.endPointServiceUserLogin = `${exports.urlServerServiceUser}/login`;
exports.endPointServiceUserInfo = `${exports.urlServerServiceUser}/user`;
exports.endPointServiceUserAdminInfo = `${exports.urlServerServiceUser}/admin`;
exports.endPointServiceUserCheckRole = `${exports.endPointServiceUserInfo}/check_role`;
exports.endPointServiceFutureUsers = `${exports.urlServerServiceUser}/future-users`;
exports.endPointServiceUserInscription = `${exports.urlServerServiceUser}/inscription`;
exports.endPointServiceUserInscriptionValide = `${exports.endPointServiceUserInscription}/valide-user/`;
exports.endPointServiceUsersInfo = `${exports.urlServerServiceUser}/users`;
exports.urlsAcceptedWithoutConnection = [
    exports.urlApi,
    exports.urlApiUser,
    exports.urlApiUserLogin,
    exports.urlApiUserInscription,
];
//# sourceMappingURL=types.js.map