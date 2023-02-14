/** Server URL */
export const urlApi = "/api";
export const urlApiUser = `${urlApi}/.user`;
export const urlApiUserLogin = `${urlApiUser}/login`;
export const urlApiUserInfo = `${urlApiUser}/user`;
export const urlApiAdminInfo = `${urlApiUser}/admin`;
export const urlApiCheckRole = `${urlApiUser}/checkRole`;
export const urlApiFutureUser = `${urlApiUser}/futureUsers`;
export const urlApiUserInscription = `${urlApiUser}/inscription`;
export const urlApiUserInscriptionValide = `${urlApiUser}/inscription/valideUser/:id`;
export const urlApiUsersInfo = `${urlApiUser}/users`;


/** Service User */
export const urlServerServiceUser = "http://caddy/api";
export const endPointServiceUserHello = `${urlServerServiceUser}/hello`;
export const endPointServiceUserLogin = `${urlServerServiceUser}/login`;
export const endPointServiceUserInfo = `${urlServerServiceUser}/user`;
export const endPointServiceUserAdminInfo = `${urlServerServiceUser}/admin`;
export const endPointServiceUserCheckRole = `${endPointServiceUserInfo}/check_role`;
export const endPointServiceFutureUsers = `${urlServerServiceUser}/future-users`;
export const endPointServiceUserInscription = `${urlServerServiceUser}/inscription`;
export const endPointServiceUserInscriptionValide = `${endPointServiceUserInscription}/valide-user/`;
export const endPointServiceUsersInfo = `${urlServerServiceUser}/users`;


export const urlsAcceptedWithoutConnection = [
  urlApi,
  urlApiUser,
  urlApiUserLogin,
  urlApiUserInscription,
];