package com.example.xstreamdemo;

import java.io.IOException;
import java.io.Serializable;

public class Car implements Serializable {
    private String name;
    private int price;

    public Car() {
        this.name = name;
        this.price = price;
    }

    public String getName() {
        System.out.println("Print getName");
        return name;
    }

    public void setName(String name) {
        System.out.println("Print setName");
        this.name = name;
    }

    public int getPrice() {
        System.out.println("Print getPrice");
        return price;
    }

    public void setPrice(int price) {
        System.out.println("Print setPrice");
        this.price = price;
    }

    private void readObject(java.io.ObjectInputStream s) throws IOException, ClassNotFoundException {
        s.defaultReadObject();
        System.out.println("Print Call readObject method");
        //Runtime.getRuntime().exec("calc");
    }
}