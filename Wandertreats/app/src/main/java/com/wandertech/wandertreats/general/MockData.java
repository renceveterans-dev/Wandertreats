package com.wandertech.wandertreats.general;

import com.wandertech.wandertreats.model.ParentModel;

import java.util.ArrayList;
import java.util.HashMap;

public class MockData {


//    public static ArrayList<ParentModel> getMockParentData(){
//
//        ArrayList<ParentModel> mainArr = new ArrayList<>();
//        mainArr.add(new ParentModel("HEllo"));
//        mainArr.add(new ParentModel("HEllo"));
//        mainArr.add(new ParentModel("HEllo"));
//        mainArr.add(new ParentModel("HEllo"));
//        mainArr.add(new ParentModel("HEllo"));
//        mainArr.add(new ParentModel("HEllo"));
//
//        return  mainArr;
//    }

//    public static ArrayList<ParentModel> getMockParentData(int no){
//
//        ArrayList<ParentModel> mainArr = new ArrayList<>();
//
//        for(int x = 0; x < no; x++){
//
//            mainArr.add(new ParentModel("HEllo"));
//        }
//      ;
//
//        return  mainArr;
//    }

    public static ArrayList<HashMap<String, String>> getMockCategoryData(){

        ArrayList<HashMap<String, String>> resArrList = new ArrayList<>();

        for(int x = 0; x < 6; x++){
            HashMap<String, String> map = new HashMap<>();

            map.put("VIEWTYPE", "ITEM");
            map.put("itemName",  "Hello");
            map.put("vImage","Hello");
            map.put("vMainStore", "Hello");


            resArrList.add(map);
        }





        return resArrList;
    }
}
