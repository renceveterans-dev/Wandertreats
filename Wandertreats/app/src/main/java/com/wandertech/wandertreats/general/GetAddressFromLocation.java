package com.wandertech.wandertreats.general;


import android.content.Context;
import android.widget.Toast;

import com.android.volley.AuthFailureError;
import com.android.volley.NetworkError;
import com.android.volley.NoConnectionError;
import com.android.volley.ParseError;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.ServerError;
import com.android.volley.TimeoutError;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.wandertech.wandertreats.utils.Constants;

import java.util.HashMap;
import java.util.Map;

public class GetAddressFromLocation {
    double latitude;
    double longitude;
    private Context mContext;
    private String serverKey;
    private GeneralFunctions generalFunc;

    private ProgressDialog myPDialog;
    private AddressFound addressFound;

    boolean isLoaderEnable = false;

    public GetAddressFromLocation(Context mContext, GeneralFunctions generalFunc) {
        this.mContext = mContext;
        this.generalFunc = generalFunc;
        serverKey = generalFunc.retrieveValue(Constants.GOOGLE_MAP_API_KEY);
    }

    public void setLocation(double latitude, double longitude) {
        this.latitude = latitude;
        this.longitude = longitude;
    }

    public void setLoaderEnable(boolean isLoaderEnable) {

        this.isLoaderEnable = isLoaderEnable;
    }

    public void execute() {

        if (isLoaderEnable) {
            myPDialog = new ProgressDialog(mContext, false);
            myPDialog.show();
        }

        StringRequest stringRequest = new StringRequest(Request.Method.POST, Constants.MAPS_SERVER,
                new Response.Listener<String>() {
                    @Override
                    public void onResponse(String response) {

                        if (myPDialog != null) {
                            myPDialog.close();
                        }

                        try {
                            addressFound.onAddressFound(generalFunc.getJsonValue("address", response), Double.parseDouble(generalFunc.getJsonValue("latitude", response)), Double.parseDouble(generalFunc.getJsonValue("longitude", response)), response);
                        }catch (Exception e){

                        }
                    }
                },

                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {

                        if (myPDialog != null) {
                            myPDialog.close();
                        }

                        if (error instanceof TimeoutError || error instanceof NoConnectionError) {
                            Toast.makeText(mContext, "No Internet Connection", Toast.LENGTH_SHORT).show();
                        } else if (error instanceof AuthFailureError) {
                            Toast.makeText(mContext, "Auth Error", Toast.LENGTH_SHORT).show();
                        } else if (error instanceof ServerError) {
                            Toast.makeText(mContext, "Server Error", Toast.LENGTH_SHORT).show();
                        } else if (error instanceof NetworkError) {
                            Toast.makeText(mContext, "Network Error", Toast.LENGTH_SHORT).show();
                        } else if (error instanceof ParseError) {
                            Toast.makeText(mContext, "Parse Error", Toast.LENGTH_SHORT).show();
                        }
                    }
                }){

            @Override
            protected Map<String,String> getParams(){
                Map<String,String> params = new HashMap<String, String>();
                params.put("ServiceType", "GET_ADDRESS_FROM_COORDINATES");
                params.put("sourceLat", String.valueOf(latitude));
                params.put("sourceLong", String.valueOf(longitude));

                return params;
            }

        };

        RequestQueue requestQueue = Volley.newRequestQueue(mContext);

        requestQueue.add(stringRequest);
        requestQueue.getCache().clear();

    }

    public void setAddressList(AddressFound addressFound) {
        this.addressFound = addressFound;
    }

    public interface AddressFound {
        void onAddressFound(String address, double latitude, double longitude, String geocodeobject);
    }
}