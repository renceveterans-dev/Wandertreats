package com.wandertech.wandertreats.general;

import android.app.Activity;
import android.content.Context;
import android.util.Log;
import android.widget.Toast;

import androidx.annotation.NonNull;

import com.android.volley.AuthFailureError;
import com.android.volley.DefaultRetryPolicy;
import com.android.volley.NetworkError;
import com.android.volley.NoConnectionError;
import com.android.volley.ParseError;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.ServerError;
import com.android.volley.TimeoutError;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.Task;
import com.google.firebase.iid.FirebaseInstanceId;
import com.google.firebase.iid.InstanceIdResult;
import com.wandertech.wandertreats.BuildConfig;
import com.wandertech.wandertreats.MyApp;
import com.wandertech.wandertreats.utils.Constants;
import com.wandertech.wandertreats.utils.DeviceData;
import com.wandertech.wandertreats.utils.Utils;

import java.util.HashMap;
import java.util.Map;

public class ExecuteWebServiceApi {
    
    public static String CUSTOM_APP_TYPE = "";
    public static String CUSTOM_UBERX_PARENT_CAT_ID = "";
    public static String DELIVERALL = "";
    public static String ONLYDELIVERALL = "";

    SetDataResponse setDataRes;
    String token = "";
    HashMap<String, String> parameters;

    GeneralFunctions generalFunc;

    String POST_URL = "";

    String responseString = "";

    boolean directUrl_value = false;
    String directUrl = "";

    boolean isLoaderShown = false;
    Context mContext;

    ProgressDialog myPDialog;

    boolean isGenerateDeviceToken = false;
    String key_DeviceToken_param;
    InternetConnection intCheck;
    boolean isSetCancelable = true;

    boolean isTaskKilled = false;

//    Call<Object> currentCall;

    /*Multi*/
    HashMap<String, Object> parametersObj;
    boolean isObjectTypeParam = false;


    public  ExecuteWebServiceApi(Context mContext, HashMap<String, String> parameters, String directUrl, boolean directUrl_value) {
        this.parameters = parameters;
        this.mContext = mContext;
        this.directUrl = directUrl;
        this.directUrl_value = directUrl_value;
    }

    public  ExecuteWebServiceApi(Context mContext, HashMap<String, Object> parametersObj, boolean isObjectTypeParam) {
        this.parametersObj = parametersObj;
        this.mContext = mContext;
        this.isObjectTypeParam = isObjectTypeParam;
    }

    public  ExecuteWebServiceApi(Context mContext, String directUrl, boolean directUrl_value) {
        this.directUrl = directUrl;
        this.directUrl_value = directUrl_value;
        this.mContext = mContext;
    }

    public void setLoaderConfig(Context mContext, boolean isLoaderShown, GeneralFunctions generalFunc) {
        this.isLoaderShown = isLoaderShown;
        this.generalFunc = generalFunc;
        this.mContext = mContext;
    }

    public void setIsDeviceTokenGenerate(boolean isGenerateDeviceToken, String key_DeviceToken_param, GeneralFunctions generalFunc) {
        this.isGenerateDeviceToken = isGenerateDeviceToken;
        this.key_DeviceToken_param = key_DeviceToken_param;
        this.generalFunc = generalFunc;
    }

    public void setCancelAble(boolean isSetCancelable) {
        this.isSetCancelable = isSetCancelable;
    }

    public void execute() {
        intCheck = new InternetConnection(mContext);

        if (!intCheck.isNetworkConnected() && !intCheck.check_int()) {
            fireResponse();
            return;
        }

        if (isLoaderShown == true) {
            //myPDialog = new MyProgressDialog(mContext, isSetCancelable, generalFunc.retrieveLangLBl("Loading", "LBL_LOADING_TXT"));
            myPDialog = new ProgressDialog(mContext, false);

            //  isSetCancelable = true;
            try {
                myPDialog.show();
            } catch (Exception e) {

            }
        }

        if (parametersObj != null) {
           //// GeneralFunctions generalFunc = MyApp.getInstance().getGeneralFun(mContext);
//            parametersObj.put("tSessionId", generalFunc.getMemberId().equals("") ? "" : generalFunc.retrieveValue(Utils.SESSION_ID_KEY));
            parametersObj.put("deviceHeight", Utils.getScreenPixelHeight(mContext) + "");
            parametersObj.put("deviceWidth", Utils.getScreenPixelWidth(mContext) + "");
            parametersObj.put("GeneralUserType", Utils.app_type);
//            parametersObj.put("GeneralMemberId", generalFunc.getMemberId());
            parametersObj.put("GeneralDeviceType", "" + Utils.deviceType);
            parametersObj.put("GeneralAppVersion", BuildConfig.VERSION_NAME);
            parametersObj.put("GeneralAppVersionCode", "" + BuildConfig.VERSION_CODE);
         //   parametersObj.put("vTimeZone", generalFunc.getTimezone());
            parametersObj.put("vUserDeviceCountry", Utils.getUserDeviceCountryCode(mContext));
            //parametersObj.put("vCurrentTime", generalFunc.getCurrentDateHourMin());
            parametersObj.put("APP_TYPE", CUSTOM_APP_TYPE);

        } else if (parameters != null) {
          //  GeneralFunctions generalFunc = MyApp.getInstance().getGeneralFun(mContext);
         // parameters.put("tSessionId", generalFunc.getMemberId().equals("") ? "" : generalFunc.retrieveValue(Utils.SESSION_ID_KEY));
            parameters.put("deviceHeight", Utils.getScreenPixelHeight(mContext) + "");
            parameters.put("deviceWidth", Utils.getScreenPixelWidth(mContext) + "");
            parameters.put("GeneralUserType", Utils.app_type);
        //  parameters.put("GeneralMemberId", generalFunc.getMemberId());
            parameters.put("GeneralDeviceType", "" + Utils.deviceType);
            parameters.put("GeneralAppVersion", BuildConfig.VERSION_NAME);
            parameters.put("GeneralAppVersionCode", "" + BuildConfig.VERSION_CODE);
          //  parameters.put("vTimeZone", generalFunc.getTimezone());
            parameters.put("vUserDeviceCountry", Utils.getUserDeviceCountryCode(mContext));
            //parameters.put("vCurrentTime", generalFunc.getCurrentDateHourMin());
            parameters.put("APP_TYPE", CUSTOM_APP_TYPE);

        }

        FirebaseInstanceId.getInstance().getInstanceId().addOnCompleteListener(new OnCompleteListener<InstanceIdResult>() {
            @Override
            public void onComplete(@NonNull Task<InstanceIdResult> task) {
                if (!task.isSuccessful()) {
                    fireResponse();
                    return;
                }
                //Toast.makeText(mContext, "FirebaseId : "+task.getResult().getToken(), Toast.LENGTH_SHORT).show();
                token = task.getResult().getToken();

                if (parameters != null) {
                    parameters.put("vFirebaseDeviceToken", task.getResult().getToken());
                }
                parameters.put("vFirebaseDeviceToken", task.getResult().getToken());
                performPostCall();
            }
        });

    }

    public void performGetCall(String directUrl) {


        StringRequest stringRequest = new StringRequest(Request.Method.POST, Constants.SERVER+directUrl,
                new com.android.volley.Response.Listener<String>() {
                    @Override
                    public void onResponse(String response) {
                        responseString = response;

                        fireResponse();

                    }
                },

                new com.android.volley.Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        responseString = error.toString();
                        fireResponse();
                        if (error instanceof TimeoutError || error instanceof NoConnectionError) {
                            // Toast.makeText(mContext, "ADL : No Internet Connection", Toast.LENGTH_SHORT).show();
                        } else if (error instanceof AuthFailureError) {
                            Toast.makeText(mContext, "ADL : Auth Error", Toast.LENGTH_SHORT).show();
                        } else if (error instanceof ServerError) {
                            Toast.makeText(mContext, "ADL : Server Error", Toast.LENGTH_SHORT).show();
                        } else if (error instanceof NetworkError) {
                            Toast.makeText(mContext, "ADL : Network Error", Toast.LENGTH_SHORT).show();
                        } else if (error instanceof ParseError) {
                            Toast.makeText(mContext, "ADL : Parse Error", Toast.LENGTH_SHORT).show();
                        }
                    }
                }){
            @Override
            protected Map<String,String> getParams(){
                Map<String,String> params = parameters;

                return params;
            }

        };

        int MY_SOCKET_TIMEOUT_MS=50000;

        stringRequest.setRetryPolicy(new DefaultRetryPolicy(
                MY_SOCKET_TIMEOUT_MS,
                DefaultRetryPolicy.DEFAULT_MAX_RETRIES,
                DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));

        RequestQueue requestQueue = Volley.newRequestQueue(mContext);

        requestQueue.add(stringRequest);
        requestQueue.getCache().clear();

    }

    public void performPostCall() {
        if (directUrl_value == true) {

            performGetCall(directUrl);
            return;
        }

        try {
            String webserviceUrl = Constants.SERVER + parameters.toString().replace(", ", "&");
            webserviceUrl = webserviceUrl.replace("{", "");
            webserviceUrl = webserviceUrl.replace("}", "");
            Log.d("Api", webserviceUrl);


            StringRequest stringRequest = new StringRequest(Request.Method.POST,   webserviceUrl,
                    new com.android.volley.Response.Listener<String>() {
                        @Override
                        public void onResponse(String response) {
                            responseString = response;

                            fireResponse();

                        }
                    },

                    new com.android.volley.Response.ErrorListener() {
                        @Override
                        public void onErrorResponse(VolleyError error) {
                            responseString = error.toString();
                            fireResponse();
                            if (error instanceof TimeoutError || error instanceof NoConnectionError) {
                                Toast.makeText(mContext, "ADL : No Internet Connection", Toast.LENGTH_SHORT).show();
                            } else if (error instanceof AuthFailureError) {
                                Toast.makeText(mContext, "ADL : Auth Error", Toast.LENGTH_SHORT).show();
                            } else if (error instanceof ServerError) {
                                Toast.makeText(mContext, "ADL : Server Error", Toast.LENGTH_SHORT).show();
                            } else if (error instanceof NetworkError) {
                                Toast.makeText(mContext, "ADL : Network Error", Toast.LENGTH_SHORT).show();
                            } else if (error instanceof ParseError) {
                                Toast.makeText(mContext, "ADL : Parse Error", Toast.LENGTH_SHORT).show();
                            }
                        }
                    }){
                @Override
                protected Map<String,String> getParams(){
                    Map<String,String> params = parameters;

                    return params;
                }

            };

            int MY_SOCKET_TIMEOUT_MS=50000;

            stringRequest.setRetryPolicy(new DefaultRetryPolicy(
                    MY_SOCKET_TIMEOUT_MS,
                    DefaultRetryPolicy.DEFAULT_MAX_RETRIES,
                    DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));


            RequestQueue requestQueue = Volley.newRequestQueue(mContext);

            requestQueue.add(stringRequest);
            requestQueue.getCache().clear();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void fireResponse() {

        if (myPDialog != null) {
            myPDialog.close();
        }

        if (setDataRes != null && !isTaskKilled) {
            GeneralFunctions generalFunc = MyApp.getInstance().getGeneralFun(mContext);
            String message = Utils.checkText(responseString) ? generalFunc.getJsonValue(Utils.message_str, responseString) : null;

            if (message != null && message.equals("DO_RESTART")) {
                generalFunc.restartApp();
                Utils.runGC();
                return;
            } else {
                try {
                    if (mContext != null && mContext instanceof Activity) {
                        Activity act = (Activity) mContext;
                        if (!act.isFinishing()) {
                            if (message != null && message.equals("SESSION_OUT")) {
                               // MyApp.getInstance().notifySessionTimeOut();
                                return;
                            }

                        }
                    }
                } catch (Exception e) {
                    Toast.makeText(mContext, "catvhError ::" + e.toString(), Toast.LENGTH_SHORT).show();
                }
                setDataRes.setResponse(responseString);
            }
        }
    }

    public void cancel(boolean value) {

        this.isTaskKilled = value;
//        if (currentCall != null) {
//
//            new AsyncTask<String, String, String>() {
//                @Override
//                protected String doInBackground(String... params) {
//                    currentCall.cancel();
//                    return "";
//                }
//            }.execute();
//        }
    }


    public void setDataResponseListener( ExecuteWebServiceApi.SetDataResponse setDataRes) {
        this.setDataRes = setDataRes;
    }

    public interface SetDataResponse {
        void setResponse(String responseString);
    }

    

}
