package org.example;

import javax.naming.Context;
import javax.naming.InitialContext;
import java.util.Hashtable;

public class Server {
    public static void main(String[] args) throws Exception{
        HelloImpl hello=new HelloImpl();
        Hashtable<String,String> env=new Hashtable<>();
        env.put(Context.INITIAL_CONTEXT_FACTORY,"com.sun.jndi.cosnaming.CNCtxFactory");
        env.put(Context.PROVIDER_URL,"iiop://127.0.0.1:1050");
        Context init=new InitialContext(env);
        init.rebind("hello",hello);
    }
}
