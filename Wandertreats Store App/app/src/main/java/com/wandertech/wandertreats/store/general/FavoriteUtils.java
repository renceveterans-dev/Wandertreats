package com.wandertech.wandertreats.store.general;

import android.content.Context;

import com.wandertech.wandertreats.store.utils.Utils;

import java.util.Arrays;
import java.util.LinkedList;
import java.util.List;

public class FavoriteUtils {
    Context a;
    GeneralFunctions generalFunctions;
    public FavoriteUtils(Context context, GeneralFunctions generalFunctions){
        this.a = context;
        this.generalFunctions = generalFunctions;
    }

    public String getFavoritemItemList() {

        return generalFunctions .retrieveValue(Utils.FAVORITE_ITEM_LIST) == null ||
                generalFunctions .retrieveValue(Utils.FAVORITE_ITEM_LIST) == "" ? "" :  generalFunctions .retrieveValue(Utils.FAVORITE_ITEM_LIST);
    }

    public void  addToFavoritemItemList(String id) {

        if(getFavoritemItemList().equalsIgnoreCase("")){
            StringBuffer favoritesItemList = new StringBuffer("");
            favoritesItemList.append(id);
            generalFunctions .storeData(Utils.FAVORITE_ITEM_LIST, favoritesItemList.toString());
        }else{
            StringBuffer favoritesItemList = new StringBuffer(getFavoritemItemList());
            favoritesItemList.append(","+id);
            generalFunctions .storeData(Utils.FAVORITE_ITEM_LIST, favoritesItemList.toString());
        }
    }

    public void removeToFavoritemItemList(String id) {

        List<String> aList = null;
        String[] stringArr;

        if(!getFavoritemItemList().equalsIgnoreCase("")){
            try {
                StringBuffer favoritesItemList = new StringBuffer(getFavoritemItemList());
                stringArr = favoritesItemList.toString().split(",");
                aList  = new LinkedList<>(Arrays.asList(stringArr));
                for(int x=0; x<stringArr.length; x++){

                    if(stringArr[x].equalsIgnoreCase(id)){
                        if(aList.size() > 0){
                            aList.remove(id);
                        }
                    }
                }
                String str = "";
                for (String value:aList) {
                    if(!str.equalsIgnoreCase("")){
                        str+=","+value;
                    }else{
                        str+=value;
                    }
                }
                generalFunctions .storeData(Utils.FAVORITE_ITEM_LIST,str);
            }catch (Exception e){
                generalFunctions .showMessage(e.toString());
            }
        }
    }

    public boolean isFavorite(String id) {

        String[] stringArr;

        if(!getFavoritemItemList().equalsIgnoreCase("")){
            try {
                StringBuffer favoritesItemList = new StringBuffer(getFavoritemItemList());
                stringArr = favoritesItemList.toString().split(",");
                for(int x=0; x<stringArr.length; x++){

                    if(stringArr[x].equalsIgnoreCase(id)){
                        return true;
                    }
                }
                return false;
            }catch (Exception e){
                return false;
            }
        }else{
            return false;
        }

    }

}
