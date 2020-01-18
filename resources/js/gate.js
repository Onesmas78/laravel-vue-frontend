export default class Gate {
    constructor(user) {
        this.user = user;
    }

    isAdmin() {
        return this.user.type === "admin";
    }
    isSubsriber() {
        return this.user.type === "subscriber";
    }
}
