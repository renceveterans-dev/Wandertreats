package com.wandertech.wandertreats.general;

import com.wandertech.wandertreats.model.ItemModel;
import com.wandertech.wandertreats.model.ParentModel;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;

public class Data {

    public static ArrayList<HashMap<String, String>> getMerchantTypeData(JSONArray jsonArray, GeneralFunctions appFunctions){

        ArrayList<HashMap<String, String>> retArrList = new ArrayList<>();
        GeneralFunctions appFunc = appFunctions;

        if(jsonArray.length() > 0 && jsonArray != null){

            for(int x = 0; x < 6; x++){

                JSONObject obj = appFunc.getJsonObject(jsonArray, x);
                HashMap<String, String> map = new HashMap<>();

                map.put("VIEWTYPE", "ITEM");
                map.put("iTypeId",  appFunc.getJsonValue("iTypeId", obj.toString()));
                map.put("vMerchantType", appFunc.getJsonValue("vMerType", obj.toString()));
                map.put("vMerchantDesc", appFunc.getJsonValue("vMerDesc", obj.toString()));
                map.put("vImage", appFunc.getJsonValue("vImage", obj.toString()));
                map.put("dCreated", appFunc.getJsonValue("dCreated", obj.toString()));
                map.put("vMainStore", "Hello");


                retArrList.add(map);
            }
        }

        return retArrList;
    }

    public static ArrayList<ItemModel> getMerchantAllListData(JSONArray jsonArray, GeneralFunctions appFunctions){
        ArrayList<ItemModel> mainArr = new ArrayList<>();
        GeneralFunctions appFunc = appFunctions;

        if(jsonArray.length() > 0 && jsonArray != null){

            for(int x = 0; x < jsonArray.length(); x++){

//                map.put("VIEWTYPE", "MERCHANT");
//                map.put("iMerchantId",  appFunc.getJsonValue("iMerchantId", obj.toString()));
//                map.put("vStoreName", appFunc.getJsonValue("vStoreName", obj.toString()));
//                map.put("vStoreDesc", appFunc.getJsonValue("vStoreDesc", obj.toString()));
//                map.put("vStoreTheme", appFunc.getJsonValue("vStoreTheme", obj.toString()));
//                map.put("vStoreLocation", appFunc.getJsonValue("vStoreLocation", obj.toString()));
//                map.put( "vUsername", appFunc.getJsonValue("vUsername", obj.toString()));
//                map.put("vStoreAddress", appFunc.getJsonValue("vStoreAddress", obj.toString()));
//                map.put( "vLatitude", appFunc.getJsonValue("vLatitude", obj.toString()));
//                map.put("vLongitude", appFunc.getJsonValue("vLongitude", obj.toString()));
//                map.put("vContactName", appFunc.getJsonValue("vContactName", obj.toString()));
//                map.put("vPhone", appFunc.getJsonValue("vPhone", obj.toString()));
//                map.put("vTelephone", appFunc.getJsonValue("vTelephone", obj.toString()));
//                map.put("vEmail", appFunc.getJsonValue("vEmail", obj.toString()));
//                map.put("vLogo", appFunc.getJsonValue("vLogo", obj.toString()));
//                map.put("vImages", appFunc.getJsonValue("vImages", obj.toString()));
//                map.put("vRatings", appFunc.getJsonValue("vRatings", obj.toString()));
//                map.put("vDocuments", appFunc.getJsonValue("vDocuments", obj.toString()));
//                map.put("ePhoneVerified", appFunc.getJsonValue("ePhoneVerified\"", obj.toString()));
//                map.put( "eEmailVerified", appFunc.getJsonValue("eEmailVerified", obj.toString()));
//                map.put("eLogout", appFunc.getJsonValue("eLogout", obj.toString()));
//                map.put( "iSessionId", appFunc.getJsonValue("iSessionId", obj.toString()));
//                map.put("vFirebaseToken", appFunc.getJsonValue("vFirebaseToken", obj.toString()));
//                map.put("iAppVersion", appFunc.getJsonValue("iAppVersion", obj.toString()));
//                map.put("eStatus", appFunc.getJsonValue("eStatus", obj.toString()));
//                map.put("dCreated", appFunc.getJsonValue("dCreated", obj.toString()));
//                map.put("data", obj.toString());

                JSONObject obj = appFunc.getJsonObject(jsonArray, x);

                mainArr.add(new ItemModel(appFunc.getJsonValue("vStoreName", obj.toString()),
                                appFunc.getJsonValue("vStoreAddress", obj.toString()),
                                appFunc.getJsonValue("vRatings", obj.toString()),
                                "",
                                appFunc.getJsonValue("vLogo", obj.toString()),
                                appFunc.getJsonValue("vImages", obj.toString()),
                                obj.toString()
                        )
                );


            }
        }


        return  mainArr;
    }

    public static ArrayList<ParentModel> getParentData(JSONArray jsonArray, GeneralFunctions appFunctions){

        ArrayList<ParentModel> mainArr = new ArrayList<>();
        GeneralFunctions appFunc = appFunctions;

        if(jsonArray.length() > 0 && jsonArray != null){

            for(int x = 0; x < jsonArray.length(); x++){

                JSONObject obj = appFunc.getJsonObject(jsonArray, x);

                mainArr.add(new ParentModel(appFunc.getJsonValue("title", obj.toString()),
                        appFunc.getJsonValue("desc", obj.toString()),
                        appFunc.getJsonValue("type", obj.toString()),
                        appFunc.getJsonValue("products", obj.toString())
                ));


            }
        }


        return  mainArr;
    }



    public static ArrayList<ItemModel> getProductData(JSONArray jsonArray, GeneralFunctions appFunctions){

        ArrayList<ItemModel> mainArr = new ArrayList<>();
        GeneralFunctions appFunc = appFunctions;

        if(jsonArray.length() > 0 && jsonArray != null){

            for(int x = 0; x < jsonArray.length(); x++){

                JSONObject obj = appFunc.getJsonObject(jsonArray, x);

                mainArr.add(new ItemModel(appFunc.getJsonValue("vProductName", obj.toString()),
                                appFunc.getJsonValue("vProductDesc", obj.toString()),
                                appFunc.getJsonValue("fPrice", obj.toString()),
                                "",
                                appFunc.getJsonValue("vThumbnail", obj.toString()),
                                appFunc.getJsonValue("vTag", obj.toString()),
                                obj.toString()
                        )
                );


            }
        }


        return  mainArr;
    }



    public static   ArrayList<String>  getProductImages(JSONArray jsonArray, GeneralFunctions appFunctions){
        try{
            ArrayList<String> imagelist = new ArrayList<>();
            GeneralFunctions appFunc = appFunctions;

            if(jsonArray.length() > 0 && jsonArray != null){

                for(int x = 0; x < jsonArray.length(); x++){

                    JSONObject obj = appFunc.getJsonObject(jsonArray, x);
                    imagelist.add( appFunc.getJsonValue("vImage", obj.toString()));

                }
            }


            return  imagelist;
        }catch (Exception e){
            return  null;
        }

    }

    public static  ArrayList<HashMap<String, String>> getNotificationData(JSONArray jsonArray, GeneralFunctions appFunctions){

        ArrayList<HashMap<String, String>> retArrList = new ArrayList<>();
        GeneralFunctions appFunc = appFunctions;

        if(jsonArray.length() > 0 && jsonArray != null){

            for(int x = 0; x < jsonArray.length(); x++){

                JSONObject obj = appFunc.getJsonObject(jsonArray, x);
                HashMap<String, String> map = new HashMap<>();

                map.put("VIEWTYPE", "TYPE");
                map.put("iNotificationId",  appFunc.getJsonValue("iNotificationId", obj.toString()));
                map.put("vUserType", appFunc.getJsonValue("vUserType", obj.toString()));
                map.put("iUserId", appFunc.getJsonValue("iUserId", obj.toString()));
                map.put("vTitle", appFunc.getJsonValue("vTitle", obj.toString()));
                map.put("vDescription", appFunc.getJsonValue("vDescription", obj.toString()));
                map.put( "vType", appFunc.getJsonValue("vType", obj.toString()));
                map.put("vImage", appFunc.getJsonValue("vImage", obj.toString()));
                map.put( "vUrl", appFunc.getJsonValue("vUrl", obj.toString()));
                map.put("vSent", appFunc.getJsonValue("vSent", obj.toString()));
                map.put("vIntent", appFunc.getJsonValue("vIntent", obj.toString()));
                map.put( "eStatus", appFunc.getJsonValue("eStatus", obj.toString()));
                map.put( "dDateCreated", appFunc.getJsonValue("dDateCreated", obj.toString()));
                retArrList.add(map);

            }
        }


        return  retArrList;
    }



    public static  ArrayList<HashMap<String, String>> getFeedsData(JSONArray jsonArray, GeneralFunctions appFunctions){

        ArrayList<HashMap<String, String>> retArrList = new ArrayList<>();
        GeneralFunctions appFunc = appFunctions;

        if(jsonArray.length() > 0 && jsonArray != null){

            for(int x = 0; x < jsonArray.length(); x++){

                JSONObject obj = appFunc.getJsonObject(jsonArray, x);
                HashMap<String, String> map = new HashMap<>();

                map.put("VIEWTYPE", "TYPE");
                map.put("iFeedId",  appFunc.getJsonValue("iFeedId", obj.toString()));
                map.put("vPublishedBy", appFunc.getJsonValue("vPublishedBy", obj.toString()));
                map.put("vTitle", appFunc.getJsonValue("vTitle", obj.toString()));
                map.put("vMessage", appFunc.getJsonValue("vMessage", obj.toString()));
                map.put("vImage", appFunc.getJsonValue("vImage", obj.toString()));
                map.put( "iReactions", appFunc.getJsonValue("iReactions", obj.toString()));
                map.put( "eUrlType", appFunc.getJsonValue("eUrlType", obj.toString()));
                map.put( "vUrl", appFunc.getJsonValue("vUrl", obj.toString()));
                map.put("dDate", appFunc.getJsonValue("dDate", obj.toString()));
                map.put( "eStatus", appFunc.getJsonValue("eStatus", obj.toString()));
                retArrList.add(map);

            }
        }


        return  retArrList;
    }

    public static  ArrayList<HashMap<String, String>> getMyPurchasedData(JSONArray jsonArray, GeneralFunctions appFunctions){

        ArrayList<HashMap<String, String>> retArrList = new ArrayList<>();
        GeneralFunctions appFunc = appFunctions;

        if(jsonArray.length() > 0 && jsonArray != null){

            for(int x = 0; x < jsonArray.length(); x++){

                JSONObject obj = appFunc.getJsonObject(jsonArray, x);
                HashMap<String, String> map = new HashMap<>();

                map.put("VIEWTYPE", "TYPE");
                map.put("iPurchaseId",  appFunc.getJsonValue("iPurchaseId", obj.toString()));
                map.put("vPurchaseNo", appFunc.getJsonValue("vPurchaseNo", obj.toString()));
                map.put("tPurchaseRequestDate", appFunc.getJsonValue("tPurchaseRequestDate", obj.toString()));
                map.put("fTotalGenerateFare", appFunc.getJsonValue("fTotalGenerateFare", obj.toString()));
                map.put("vPurchaseName", appFunc.getJsonValue("vPurchaseName", obj.toString()));
                map.put("dValidity",  appFunc.getJsonValue("dValidity", obj.toString()));
                map.put("vName", appFunc.getJsonValue("vName", obj.toString()));
                map.put("vLastName", appFunc.getJsonValue("vLastName", obj.toString()));
                map.put("data", obj.toString());
                retArrList.add(map);

            }
        }


        return  retArrList;
    }

    public static String getPesoSymbol(){
        return "₱";
    }


    public static ArrayList<HashMap<String, String>> getMerchantData(JSONArray jsonArray, GeneralFunctions appFunctions){

        ArrayList<HashMap<String, String>> retArrList = new ArrayList<>();
        GeneralFunctions appFunc = appFunctions;

        if(jsonArray.length() > 0 && jsonArray != null){

            for(int x = 0; x < jsonArray.length(); x++){

                JSONObject obj = appFunc.getJsonObject(jsonArray, x);
                HashMap<String, String> map = new HashMap<>();

                map.put("VIEWTYPE", "MERCHANT");
                map.put("iMerchantId",  appFunc.getJsonValue("iMerchantId", obj.toString()));
                map.put("vStoreName", appFunc.getJsonValue("vStoreName", obj.toString()));
                map.put("vStoreDesc", appFunc.getJsonValue("vStoreDesc", obj.toString()));
                map.put("vStoreTheme", appFunc.getJsonValue("vStoreTheme", obj.toString()));
                map.put("vStoreLocation", appFunc.getJsonValue("vStoreLocation", obj.toString()));
                map.put( "vUsername", appFunc.getJsonValue("vUsername", obj.toString()));
                map.put("vStoreAddress", appFunc.getJsonValue("vStoreAddress", obj.toString()));
                map.put( "vLatitude", appFunc.getJsonValue("vLatitude", obj.toString()));
                map.put("vLongitude", appFunc.getJsonValue("vLongitude", obj.toString()));
                map.put("vContactName", appFunc.getJsonValue("vContactName", obj.toString()));
                map.put("vPhone", appFunc.getJsonValue("vPhone", obj.toString()));
                map.put("vTelephone", appFunc.getJsonValue("vTelephone", obj.toString()));
                map.put("vEmail", appFunc.getJsonValue("vEmail", obj.toString()));
                map.put("vLogo", appFunc.getJsonValue("vLogo", obj.toString()));
                map.put("vImages", appFunc.getJsonValue("vImages", obj.toString()));
                map.put("vRatings", appFunc.getJsonValue("vRatings", obj.toString()));
                map.put("vDocuments", appFunc.getJsonValue("vDocuments", obj.toString()));
                map.put("ePhoneVerified", appFunc.getJsonValue("ePhoneVerified\"", obj.toString()));
                map.put( "eEmailVerified", appFunc.getJsonValue("eEmailVerified", obj.toString()));
                map.put("eLogout", appFunc.getJsonValue("eLogout", obj.toString()));
                map.put( "iSessionId", appFunc.getJsonValue("iSessionId", obj.toString()));
                map.put("vFirebaseToken", appFunc.getJsonValue("vFirebaseToken", obj.toString()));
                map.put("iAppVersion", appFunc.getJsonValue("iAppVersion", obj.toString()));
                map.put("eStatus", appFunc.getJsonValue("eStatus", obj.toString()));
                map.put("dCreated", appFunc.getJsonValue("dCreated", obj.toString()));
                map.put("data", obj.toString());


                retArrList.add(map);
            }
        }


        return retArrList;
    }

    public static ArrayList<HashMap<String, String>> getPaymentData(JSONArray jsonArray, GeneralFunctions appFunctions){

        ArrayList<HashMap<String, String>> retArrList = new ArrayList<>();
        GeneralFunctions appFunc = appFunctions;

        if(jsonArray.length() > 0 && jsonArray != null){

            for(int x = 0; x < jsonArray.length(); x++){

                JSONObject obj = appFunc.getJsonObject(jsonArray, x);
                HashMap<String, String> map = new HashMap<>();


                map.put("title",  appFunc.getJsonValue("vPaymentName", obj.toString()));
                map.put("message", appFunc.getJsonValue("vPaymentDesc", obj.toString()));
                map.put("selected", "No");
                map.put("data", obj.toString());


                retArrList.add(map);
            }
        }







        return retArrList;
    }

}
