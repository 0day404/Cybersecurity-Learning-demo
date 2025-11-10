package com.example.xstreamdemo;

import java.beans.XMLDecoder;
import java.beans.XMLEncoder;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;

public class Main {
    public static void main(String[] args) throws IOException {

        //start执行命令方式1
        //new ProcessBuilder("cmd.exe", "/c", "calc.exe").start();

        //start执行命令方式2
        //new ProcessBuilder("calc.exe").start();

        //创建encoder对象并序列化输出output.xml文件
//        Car car = new Car();
//        XMLEncoder encoder = new XMLEncoder(new FileOutputStream("output1.xml"));
//        //使用writeObject方法 序列化对象
//        encoder.writeObject(car);
//        encoder.close();

         //创建decoder并反序列化calc.xml对象
        XMLDecoder decoder = new XMLDecoder(new FileInputStream("calc.xml"));
        Car car2 = (Car) decoder.readObject();
    }
}
