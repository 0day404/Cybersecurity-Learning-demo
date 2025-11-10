package org.example;

import javax.naming.Context;
import javax.naming.InitialContext;
import javax.rmi.PortableRemoteObject;
import java.util.Hashtable;

public class Client {
    public static void main(String[] args) throws Exception{
        Hashtable<String,String> env=new Hashtable<>();
        env.put(Context.INITIAL_CONTEXT_FACTORY,"com.sun.jndi.cosnaming.CNCtxFactory");
        env.put(Context.PROVIDER_URL,"iiop://localhost:1050");
        InitialContext ic=new InitialContext(env);
        Object objref=ic.lookup("hello");
        HelloInterface hi= (HelloInterface) PortableRemoteObject.narrow(objref,HelloInterface.class);
        Message message=new Message();
        hi.sayHello(message);

    }
}
