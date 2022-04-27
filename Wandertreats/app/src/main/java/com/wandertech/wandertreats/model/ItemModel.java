package com.wandertech.wandertreats.model;

public class ItemModel {

    String title;
    String description;
    String price;
    String button;
    String thumbnail;
    String images;
    String data;

    public ItemModel(String title, String description, String price,  String btn, String thumbnail, String images, String data){
        this.title = title;
        this.description = description;
        this.button = btn;
        this.price = price;
        this.thumbnail = thumbnail;
        this.images = images;
        this.data = data;
    }

    public String getButton() {
        return button;
    }

    public String getDescription() {
        return description;
    }

    public String getTitle() {
        return title;
    }

    public void setButton(String button) {
        this.button = button;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public void setTitle(String title) {
        this.title = title;
    }

    public String getImages() {
        return images;
    }

    public String getPrice() {
        return price;
    }

    public String getThumbnail() {
        return thumbnail;
    }

    public void setImages(String images) {
        this.images = images;
    }

    public void setPrice(String price) {
        this.price = price;
    }

    public void setThumbnail(String thumbnail) {
        this.thumbnail = thumbnail;
    }

    public String getData() {
        return data;
    }

    public void setData(String data) {
        this.data = data;
    }
}
