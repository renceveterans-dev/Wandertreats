package com.wandertech.wandertreats.model;

public class ParentModel {

    String mainTitle;
    String mainDesc;
    String mainType;
    String mainProducts;

    public ParentModel(String mainTitle, String mainDesc, String mainType, String mainProducts) {
        this.mainTitle = mainTitle;
        this.mainDesc = mainDesc;
        this.mainType = mainType;
        this.mainProducts = mainProducts;
    }

    public String getMainTitle() {
        return mainTitle;
    }

    public void setMainTitle(String mainTitle) {
        this.mainTitle = mainTitle;
    }

    public String getMainDesc() {
        return mainDesc;
    }

    public String getMainType() {
        return mainType;
    }

    public void setMainDesc(String mainDesc) {
        this.mainDesc = mainDesc;
    }

    public void setMainProducts(String mainProducts) {
        this.mainProducts = mainProducts;
    }

    public void setMainType(String mainType) {
        this.mainType = mainType;
    }

    public String getMainProducts() {
        return mainProducts;
    }
}
