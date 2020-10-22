export default class gates{

    constructor(user){
        this.user = user;
    }

    is_admin(){
        return this.user.type === 'admin';
    }

    is_user(){
        return this.user.type === 'user';
    }

}