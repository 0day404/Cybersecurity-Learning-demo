package org.example;

import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;

public class Message implements java.io.Serializable{
    private void readObject(ObjectInputStream e)throws Exception{
        System.out.println("readObject");
        Runtime.getRuntime().exec("calc.exe");
    }
    private void writeObject(ObjectOutputStream e)throws Exception{
        System.out.println("writeObject");
    }

    public String getMsg() {
        return "getMsg";
    }
}

