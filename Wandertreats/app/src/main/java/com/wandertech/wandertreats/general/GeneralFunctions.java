package com.wandertech.wandertreats.general;

import android.animation.ObjectAnimator;
import android.animation.ValueAnimator;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.ActivityManager;
import android.app.AppOpsManager;
import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.content.pm.Signature;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Canvas;
import android.graphics.Matrix;
import android.graphics.Paint;
import android.graphics.Rect;
import android.graphics.Typeface;
import android.graphics.drawable.BitmapDrawable;
import android.location.Location;
import android.location.LocationManager;
import android.net.Uri;
import android.os.Binder;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.preference.PreferenceManager;
import android.provider.Settings;
import android.text.SpannableStringBuilder;
import android.text.TextUtils;
import android.text.format.DateFormat;
import android.text.method.LinkMovementMethod;
import android.util.Base64;
import android.util.Log;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.ViewTreeObserver;
import android.view.Window;
import android.view.WindowManager;
import android.view.animation.DecelerateInterpolator;
import android.view.animation.Interpolator;
import android.view.inputmethod.InputMethodManager;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.browser.customtabs.CustomTabsIntent;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;

import com.google.android.gms.common.GoogleApiAvailability;
import com.google.android.material.snackbar.Snackbar;
import com.google.android.material.textfield.TextInputEditText;
import com.wandertech.wandertreats.LauncherActivity;
import com.wandertech.wandertreats.MainActivity;
import com.wandertech.wandertreats.R;
import com.wandertech.wandertreats.utils.Constants;
import com.wandertech.wandertreats.utils.Utils;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.lang.reflect.Method;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.text.DecimalFormat;
import java.text.DecimalFormatSymbols;
import java.text.NumberFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.Iterator;
import java.util.LinkedList;
import java.util.List;
import java.util.Locale;
import java.util.Map;
import java.util.TimeZone;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class GeneralFunctions {
    public static final int MY_PERMISSIONS_REQUEST = 51;
    public static final int MY_SETTINGS_REQUEST = 52;
    Context a;
    String b = "";
    Map<String, Object> c = null;

    public GeneralFunctions(Context context, int backImgViewID) {
        this.a = context;
    }

    public GeneralFunctions(Context context) {
        this.a = context;
       // this.a(this.a);
    }

    public static String retrieveValue(String key, Context mContext) {
        SharedPreferences var2 = PreferenceManager.getDefaultSharedPreferences(mContext);
        String var3 = var2.getString(key, "");
        return var3;
    }

    public void initDedaultData(){

        this.storeData(Utils.CURRENT_ADDRESSS, "");
        this.storeData(Utils.CURRENT_LATITUDE, "0.0");
        this.storeData(Utils.CURRENT_LONGITUDE, "0.0");
        this.storeData(Utils.isUserLogIn, "0");
        this.storeData(Utils.iMemberId, "");
        this.storeData(Utils.USER_PROFILE_JSON, "");
        this.storeData(Utils.USER_FIRST_LOGIN, "0");
        this.storeData(Constants.FIRST_LAUNCH, "false");
        this.storeData(Utils.APP_GENERAL_DATA, "");

    }


    public static Bitmap rotateBitmap(Bitmap bitmap, int orientation) {
        Matrix var2 = new Matrix();
        switch(orientation) {
            case 1:
                return bitmap;
            case 2:
                var2.setScale(-1.0F, 1.0F);
                break;
            case 3:
                var2.setRotate(180.0F);
                break;
            case 4:
                var2.setRotate(180.0F);
                var2.postScale(-1.0F, 1.0F);
                break;
            case 5:
                var2.setRotate(90.0F);
                var2.postScale(-1.0F, 1.0F);
                break;
            case 6:
                var2.setRotate(90.0F);
                break;
            case 7:
                var2.setRotate(-90.0F);
                var2.postScale(-1.0F, 1.0F);
                break;
            case 8:
                var2.setRotate(-90.0F);
                break;
            default:
                return bitmap;
        }

        try {
            Bitmap var3 = Bitmap.createBitmap(bitmap, 0, 0, bitmap.getWidth(), bitmap.getHeight(), var2, true);
            bitmap.recycle();
            return var3;
        } catch (OutOfMemoryError var4) {
            var4.printStackTrace();
            return bitmap;
        }
    }

    public int getStatusBarHeight(){
        int statusBarHeight = 0;
        int resourceId = a.getResources().getIdentifier("status_bar_height", "dimen", "android");
        if (resourceId > 0) {
            statusBarHeight = a.getResources().getDimensionPixelSize(resourceId);
        }

        return statusBarHeight;
    }

    protected void hideSoftKeyboard( TextInputEditText input) {
        InputMethodManager imm = (InputMethodManager) a.getSystemService(Context.INPUT_METHOD_SERVICE);
        imm.hideSoftInputFromWindow(input.getWindowToken(), 0);
    }

    public boolean isFirstTimeLaunch() {
        return this.retrieveValue(Constants.FIRST_LAUNCH).equalsIgnoreCase("true") || this.retrieveValue(Constants.FIRST_LAUNCH).equalsIgnoreCase(null) || this.retrieveValue(Constants.FIRST_LAUNCH).isEmpty() || this.retrieveValue(Constants.FIRST_LAUNCH).equals(null) ? true : false;
    }

    public void setFirstTimeLaunch(Boolean value) {

        if (value) {
            this.storeData(Constants.FIRST_LAUNCH, "true");
        } else {
            this.storeData(Constants.FIRST_LAUNCH, "false");
        }

    }

    public long findDifference(String start_date) {

        long totalSeconds = 0;

        Calendar cal = Calendar.getInstance();
        SimpleDateFormat sdf1 = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        String strDate = sdf1.format(cal.getTime());
        System.out.println("Current date in String Format: " + strDate);

        // SimpleDateFormat converts the
        // string format to date object
        SimpleDateFormat sdf
                = new SimpleDateFormat(
                "yyyy-MM-dd HH:mm:ss");

        // Try Block
        try {

            // parse method is used to parse
            // the text from a string to
            // produce the date
            Date d1 = sdf.parse(start_date);
            Date d2 = sdf.parse(strDate);

            // Calucalte time difference
            // in milliseconds
            long difference_In_Time
                    = d2.getTime() - d1.getTime();

            // Calucalte time difference in
            // seconds, minutes, hours, years,
            // and days
            long difference_In_Seconds
                    = (difference_In_Time
                    / 1000)
                    % 60;

            long difference_In_Minutes
                    = (difference_In_Time
                    / (1000 * 60))
                    % 60;

            long difference_In_Hours
                    = (difference_In_Time
                    / (1000 * 60 * 60))
                    % 24;

            long difference_In_Years
                    = (difference_In_Time
                    / (1000l * 60 * 60 * 24 * 365));

            long difference_In_Days
                    = (difference_In_Time
                    / (1000 * 60 * 60 * 24))
                    % 365;

            // Print the date difference in
            // years, in days, in hours, in
            // minutes, and in seconds

            System.out.print(
                    "Difference "
                            + "between two dates is: ");

            System.out.println(
                    difference_In_Years
                            + " years, "
                            + difference_In_Days
                            + " days, "
                            + difference_In_Hours
                            + " hours, "
                            + difference_In_Minutes
                            + " minutes, "
                            + difference_In_Seconds
                            + " seconds");

            totalSeconds = difference_In_Time/1000;

            return totalSeconds;
        }

        // Catch the Exception
        catch (ParseException e) {
            e.printStackTrace();
            return totalSeconds;
        }
    }

    public long findDifference(String start_date,String end_date) {

        long totalSeconds = 0;

        Calendar cal = Calendar.getInstance();
        SimpleDateFormat sdf1 = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        String strDate = sdf1.format(cal.getTime());
        System.out.println("Current date in String Format: " + strDate);

        // SimpleDateFormat converts the
        // string format to date object
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");

        // Try Block
        try {

            // parse method is used to parse
            // the text from a string to
            // produce the date
            Date d1 = sdf.parse(start_date);
            Date d2 = sdf.parse(end_date);

            // Calucalte time difference
            // in milliseconds
            long difference_In_Time
                    = d2.getTime() - d1.getTime();

            // Calucalte time difference in
            // seconds, minutes, hours, years,
            // and days
            long difference_In_Seconds
                    = (difference_In_Time
                    / 1000)
                    % 60;

            long difference_In_Minutes
                    = (difference_In_Time
                    / (1000 * 60))
                    % 60;

            long difference_In_Hours
                    = (difference_In_Time
                    / (1000 * 60 * 60))
                    % 24;

            long difference_In_Years
                    = (difference_In_Time
                    / (1000l * 60 * 60 * 24 * 365));

            long difference_In_Days
                    = (difference_In_Time
                    / (1000 * 60 * 60 * 24))
                    % 365;

            // Print the date difference in
            // years, in days, in hours, in
            // minutes, and in seconds

            System.out.print(
                    "Difference "
                            + "between two dates is: ");

            System.out.println(
                    difference_In_Years
                            + " years, "
                            + difference_In_Days
                            + " days, "
                            + difference_In_Hours
                            + " hours, "
                            + difference_In_Minutes
                            + " minutes, "
                            + difference_In_Seconds
                            + " seconds");

            totalSeconds = difference_In_Time/1000;

            return totalSeconds;
        }

        // Catch the Exception
        catch (ParseException e) {
            e.printStackTrace();
            return totalSeconds;
        }
    }

    public static String convertDecimalPlaceDisplay(double val) {
        return String.format("%.2f", val).replace(",", ".");
    }

    public static String getCurrencySymbol(){
        return "₱";
    }

    @SuppressLint("WrongConstant")
    public static Intent getPreviousIntent(Context context) {
        Intent var1 = null;
        ActivityManager var2 = (ActivityManager) context.getSystemService("activity");
        List var3 = var2.getRecentTasks(1024, 0);
        String var4 = context.getPackageName();
        if (!var3.isEmpty()) {
            for (int var6 = 0; var6 < var3.size(); ++var6) {
                ActivityManager.RecentTaskInfo var5 = (ActivityManager.RecentTaskInfo) var3.get(var6);
                if (var5.baseIntent.getComponent().getPackageName().equals(var4)) {
                    var1 = var5.baseIntent;
                    var1.setFlags(268435456);
                }
            }
        }

        return var1;
    }


    public static boolean checkDataAvail(String key, String response) {
        try {
            JSONObject var2 = new JSONObject(response);
            String var3 = var2.getString(key);
            return !var3.equals("") && !var3.equals("0") && var3.equals("1");
        } catch (JSONException var4) {
            return false;
        }
    }

    public static boolean isJsonObj(String json) {
        try {
            new JSONObject(json);
            return true;
        } catch (Exception var2) {
            return false;
        }
    }

//    public static Object getTypeOfJson(String data) {
//        try {
//            Object var1 = (new JSONTokener(data)).nextValue();
//            if (var1 instanceof JSONObject) {
//                return new JsonObject();
//            }
//
//            if (var1 instanceof JSONArray) {
//                return new JsonArray();
//            }
//        } catch (Exception var2) {
//        }
//
//        return null;
//    }

    public static boolean isJSONValid(String test) {
        try {
            new JSONObject(test);
        } catch (JSONException var4) {
            try {
                new JSONArray(test);
            } catch (JSONException var3) {
                return false;
            }
        }

        return true;
    }

    public static Float parseFloatValue(float defaultValue, String strValue) {
        try {
            float var2 = Float.parseFloat(strValue);
            return var2;
        } catch (Exception var3) {
            return defaultValue;
        }
    }

    public static Double parseDoubleValue(double defaultValue, String strValue) {
        try {
            double var3 = Double.parseDouble(strValue.replace(",", ""));
            return var3;
        } catch (Exception var5) {
            return defaultValue;
        }
    }

    public static int parseIntegerValue(int defaultValue, String strValue) {
        try {
            int var2 = Integer.parseInt(strValue);
            return var2;
        } catch (Exception var3) {
            return defaultValue;
        }
    }

    public static long parseLongValue(long defaultValue, String strValue) {
        try {
            long var3 = Long.parseLong(strValue);
            return var3;
        } catch (Exception var5) {
            return defaultValue;
        }
    }

    public static DecimalFormat decimalFormat() {
        DecimalFormat var0 = new DecimalFormat("#.00");
        DecimalFormatSymbols var1 = DecimalFormatSymbols.getInstance();
        var1.setDecimalSeparator('.');
        var0.setDecimalFormatSymbols(var1);
        return var0;
    }

    public static boolean canDrawOverlaysUsingReflection(Context context) {
        try {
            if (Build.VERSION.SDK_INT >= 19) {
                @SuppressLint("WrongConstant") AppOpsManager var1 = (AppOpsManager)context.getSystemService("appops");
                Class var2 = AppOpsManager.class;
                Method var3 = var2.getMethod("checkOp", Integer.TYPE, Integer.TYPE, String.class);
                int var4 = (Integer)var3.invoke(var1, 24, Binder.getCallingUid(), context.getApplicationContext().getPackageName());
                return 0 == var4;
            } else {
                return true;
            }
        } catch (Exception var5) {
            return false;
        }
    }

    public static void storeData(String key, String data, Context mContext) {
        SharedPreferences var3 = PreferenceManager.getDefaultSharedPreferences(mContext);
        SharedPreferences.Editor var4 = var3.edit();
        var4.putString(key, data);
        var4.commit();
    }

    public static double calculationByLocation(double lat1, double lon1, double lat2, double lon2, String returnType) {
        short var9 = 6371;
        double var18 = Math.toRadians(lat2 - lat1);
        double var20 = Math.toRadians(lon2 - lon1);
        double var22 = Math.sin(var18 / 2.0D) * Math.sin(var18 / 2.0D) + Math.cos(Math.toRadians(lat1)) * Math.cos(Math.toRadians(lat2)) * Math.sin(var20 / 2.0D) * Math.sin(var20 / 2.0D);
        double var24 = 2.0D * Math.asin(Math.sqrt(var22));
        double var26 = (double)var9 * var24;
        double var28 = var26 / 1.0D;
        DecimalFormat var30 = new DecimalFormat("####");
        int var31 = Integer.valueOf(var30.format(var28));
        double var32 = var26 % 1000.0D;
        if (returnType.equalsIgnoreCase("KM")) {
            return (double)var9 * var24;
        } else {
            return returnType.equalsIgnoreCase("Miles") ? (double)var9 * var24 * 0.621371D : var32;
        }
    }

//    private void a(Context var1) {
//        new InfoProvider(var1);
//    }

    public double round(double value, int places) {
        if (places < 0) {
            throw new IllegalArgumentException();
        } else {
            long var4 = (long)Math.pow(10.0D, (double)places);
            value *= (double)var4;
            long var6 = Math.round(value);
            return (double)var6 / (double)var4;
        }
    }

    public String getDateFormatedType(String date, String originalformate, String targateformate, Locale locale) {
        String var5 = "";
        SimpleDateFormat var6 = new SimpleDateFormat(originalformate, locale);
        SimpleDateFormat var7 = new SimpleDateFormat(targateformate, locale);

        try {
            Date var8 = var6.parse(date);
            var5 = var7.format(var8);
        } catch (ParseException var9) {
            var9.printStackTrace();
        }

        return var5;
    }

    public Typeface getDefaultFont(Context context) {
        return Typeface.createFromAsset(context.getAssets(), "fonts/poppins_regular.ttf");
    }

    public boolean isPermisionGranted() {
        int var1 = ContextCompat.checkSelfPermission(this.a, "android.permission.WRITE_EXTERNAL_STORAGE");
        int var2 = ContextCompat.checkSelfPermission(this.a, "android.permission.CAMERA");
        return var1 == 0 && var2 == 0;
    }

    public String getTimezone() {
        TimeZone var1 = TimeZone.getDefault();
        return var1.getID() + "";
    }

//    public GenerateAlertBox notifyRestartApp(String message) {
//        GenerateAlertBox var2 = new GenerateAlertBox(this.a);
//        var2.setContentMessage(this.retrieveLangLBl("", "LBL_BTN_TRIP_CANCEL_CONFIRM_TXT"), "");
//        var2.setPositiveBtn(this.retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
//        var2.showAlertBox();
//        return var2;
//    }

//    public void logOUTFrmFB() {
//        LoginManager.getInstance().logOut();
//    }

//    private void a(Context var1) {
//        if (this.a instanceof Activity) {
//            if (!this.retrieveValue(Utils.LANGUAGE_IS_RTL_KEY).equals("") && this.retrieveValue(Utils.LANGUAGE_IS_RTL_KEY).equals(Utils.DATABASE_RTL_STR)) {
//                this.forceRTLIfSupported((Activity)this.a);
//                View var2 = this.getCurrentView((Activity)this.a);
//                if (var2.findViewById(var1) != null && var2.findViewById(var1) instanceof ImageView) {
//                    ImageView var3 = (ImageView)var2.findViewById(var1);
//                    if (var3.getRotation() != 180.0F) {
//                        var3.setRotation(180.0F);
//                    }
//                }
//            } else {
//                this.forceLTRIfSupported((Activity)this.a);
//            }
//        }
//
//    }

    @SuppressLint("WrongConstant")
    public void forceRTLIfSupported(Activity act) {
        if (Build.VERSION.SDK_INT >= 17) {
            act.getWindow().getDecorView().setLayoutDirection(1);
        }

    }

    @SuppressLint("WrongConstant")
    public void forceLTRIfSupported(Activity act) {
        if (Build.VERSION.SDK_INT >= 17) {
            act.getWindow().getDecorView().setLayoutDirection(0);
        }

    }

//    public void forceRTLIfSupported(AlertDialog alertDialog) {
//        if (Build.VERSION.SDK_INT >= 17) {
//            alertDialog.getWindow().getDecorView().setLayoutDirection(1);
//        }
//
//    }
//
//    public void forceRTLIfSupported(GenerateAlertBox generateAlert) {
//        if (Build.VERSION.SDK_INT >= 17) {
//            generateAlert.alertDialog.getWindow().getDecorView().setLayoutDirection(1);
//        }
//
//    }
//
//    public String wrapHtml(Context context, String html) {
//        return context.getString(this.isRTLmode() ? string.html_rtl : string.html, new Object[]{html});
//    }

    public String getDateFormatedType(String date, String originalformate, String targateformate) {
        String var4 = "";
        Locale var5 = new Locale(this.retrieveValue(Utils.LANGUAGE_CODE_KEY));
        SimpleDateFormat var6 = new SimpleDateFormat(originalformate);
        SimpleDateFormat var7 = new SimpleDateFormat(targateformate, var5);

        try {
            Date var8 = var6.parse(date);
            var4 = var7.format(var8);
        } catch (ParseException var9) {
            var9.printStackTrace();
        }

        return var4;
    }

    public String getHasKey(Context act) {
        try {
            @SuppressLint("WrongConstant") PackageInfo var2 = act.getPackageManager().getPackageInfo(act.getPackageName(), 64);
            Signature[] var3 = var2.signatures;
            int var4 = var3.length;
            byte var5 = 0;
            if (var5 < var4) {
                Signature var6 = var3[var5];
                MessageDigest var7 = MessageDigest.getInstance("SHA");
                var7.update(var6.toByteArray());
                String var8 = new String(Base64.encode(var7.digest(), 0));
                return var8;
            }
        } catch (PackageManager.NameNotFoundException var9) {
            System.out.println("name not found:" + var9.toString());
        } catch (NoSuchAlgorithmException var10) {
            System.out.println("no such an algorithm:" + var10.toString());
        } catch (Exception var11) {
            System.out.println("exception:" + var11.toString());
        }

        return "";
    }

    @SuppressLint("WrongConstant")
    public void forceRTLIfSupported(Dialog alertDialog) {
        if (Build.VERSION.SDK_INT >= 17) {
            alertDialog.getWindow().getDecorView().setLayoutDirection(1);
        }

    }

    @SuppressLint("WrongConstant")
    public void forceLTRIfSupported(Dialog alertDialog) {
        if (Build.VERSION.SDK_INT >= 17) {
            alertDialog.getWindow().getDecorView().setLayoutDirection(0);
        }

    }

//    public boolean isRTLmode() {
//        return !this.retrieveValue(Utils.LANGUAGE_IS_RTL_KEY).equals("") && this.retrieveValue(Utils.LANGUAGE_IS_RTL_KEY).equals(Utils.DATABASE_RTL_STR);
//    }

    public String retrieveValue(String key) {
        SharedPreferences var2 = PreferenceManager.getDefaultSharedPreferences(this.a);
        String var3 = var2.getString(key, "");
        return var3;
    }

//    public String retrieveLangLBl(String orig, String label) {
//        if (this.isLanguageLabelsAvail()) {
//            if (this.b.equals("")) {
//                SharedPreferences var3 = PreferenceManager.getDefaultSharedPreferences(this.a);
//                String var4 = var3.getString(Utils.languageLabelsKey, "");
//                this.b = var4;
//            }
//
//            if (this.c == null && !this.b.equals("")) {
//                JSONObject var8 = this.getJsonObject(this.b);
//                HashMap var9 = new HashMap();
//
//                String var6;
//                Object var7;
//                for(Iterator var5 = var8.keys(); var5.hasNext(); var9.put(var6, var7)) {
//                    var6 = (String)var5.next();
//                    var7 = this.getJsonValue(var6, var8);
//                    if (var7 instanceof JSONArray) {
//                        var7 = this.toList((JSONArray)var7);
//                    } else if (var7 instanceof JSONObject) {
//                        var7 = this.toMap((JSONObject)var7);
//                    }
//                }
//
//                this.c = var9;
//            }
//
//            if (this.c != null) {
//                if (this.c.get(label) != null) {
//                    return (String)this.c.get(label);
//                } else {
//                    return orig.equals("") ? (label.startsWith("LBL_") ? orig : label) : orig;
//                }
//            } else if (this.getJsonValue(label, this.b).equals("")) {
//                return orig.equals("") ? (label.startsWith("LBL_") ? orig : label) : orig;
//            } else {
//                return this.getJsonValue(label, this.b);
//            }
//        } else {
//            return orig.equals("") ? (label.startsWith("LBL_") ? orig : label) : orig;
//        }
//    }

    public List<Object> toList(JSONArray array) {
        ArrayList var2 = new ArrayList();

        for(int var3 = 0; var3 < array.length(); ++var3) {
            Object var4 = null;

            try {
                var4 = array.get(var3);
            } catch (JSONException var6) {
                var6.printStackTrace();
            }

            if (var4 != null) {
                if (var4 instanceof JSONArray) {
                    var4 = this.toList((JSONArray)var4);
                } else if (var4 instanceof JSONObject) {
                    var4 = this.toMap((JSONObject)var4);
                }

                var2.add(var4);
            }
        }

        return var2;
    }

    public Map<String, Object> toMap(JSONObject object) {
        HashMap var2 = new HashMap();

        String var4;
        Object var5;
        for(Iterator var3 = object.keys(); var3.hasNext(); var2.put(var4, var5)) {
            var4 = (String)var3.next();
            var5 = this.getJsonValue(var4, object);
            if (var5 instanceof JSONArray) {
                var5 = this.toList((JSONArray)var5);
            } else if (var5 instanceof JSONObject) {
                var5 = this.toMap((JSONObject)var5);
            }
        }

        return var2;
    }

    public Object getJsonValue(String key, JSONObject response) {
        try {
            if (response != null) {
                Object var3 = response.get(key);
                if (var3 != null && !var3.equals("null") && !var3.equals("")) {
                    return var3;
                }
            }

            return "";
        } catch (JSONException var4) {
            var4.printStackTrace();
            return "";
        }
    }

    public String getJsonValueStr(String key, JSONObject response) {
        try {
            if (response != null) {
                String var3 = "";
                if (response.has(key)) {
                    var3 = response.getString(key);
                }

                if (var3 != null && !var3.equals("null") && !var3.equals("")) {
                    return var3;
                }
            }

            return "";
        } catch (JSONException var4) {
            return "";
        }
    }

//    public String generateDeviceToken() {
//        if (!this.checkPlayServices()) {
//            return "";
//        } else {
//            String var1 = "";
//
//            try {
//                var1 = FirebaseInstanceId.getInstance().getToken(this.retrieveValue(Utils.APP_GCM_SENDER_ID_KEY), "FCM");
//            } catch (Exception var3) {
//                var3.printStackTrace();
//                var1 = "";
//            }
//
//            return var1;
//        }
//    }

    public boolean checkPlayServices() {
        GoogleApiAvailability var1 = GoogleApiAvailability.getInstance();
        int var2 = var1.isGooglePlayServicesAvailable(this.a);
        if (var2 != 0) {
            if (var1.isUserResolvableError(var2)) {
                ((Activity)this.a).runOnUiThread(() -> {
                    var1.getErrorDialog((Activity)this.a, var2, 9000).show();
                });
            }

            return false;
        } else {
            return true;
        }
    }

    public void removeValue(String key) {
        SharedPreferences var2 = PreferenceManager.getDefaultSharedPreferences(this.a);
        SharedPreferences.Editor var3 = var2.edit();
        var3.remove(key);
        var3.commit();
    }

    public boolean containsKey(String key) {
        SharedPreferences var2 = PreferenceManager.getDefaultSharedPreferences(this.a);
        SharedPreferences.Editor var3 = var2.edit();
        String var4 = var2.getString(key, (String)null);
        return var4 != null;
    }

    public void storeData(String key, String data) {
        SharedPreferences var3 = PreferenceManager.getDefaultSharedPreferences(this.a);
        SharedPreferences.Editor var4 = var3.edit();
        var4.putString(key, data);
        var4.commit();
    }

    public String addSemiColonToPrice(String value) {
        return NumberFormat.getNumberInstance(Locale.US).format((long)parseIntegerValue(0, value));
    }

    public void storeUserData(String memberId) {
        this.storeData(Utils.iMemberId, memberId);
        this.storeData(Utils.isUserLogIn, "1");
    }

    public String getMemberId() {
        return this.isUserLoggedIn() ? this.retrieveValue(Utils.iMemberId) : "";
    }

    public String getFirstName() {

        String profileData = this.retrieveValue(Utils.USER_PROFILE_JSON);
        return this.getJsonValue("vName", profileData);
    }

    public String getFullName() {

        String profileData = this.retrieveValue(Utils.USER_PROFILE_JSON);
        return this.getJsonValue("vName", profileData)+" "+this.getJsonValue("vLastName", profileData);
    }

    public String getEmail() {

        String profileData = this.retrieveValue(Utils.USER_PROFILE_JSON);
        return this.getJsonValue("vEmail", profileData);
    }


    public String getMobileNumber() {

        String profileData = this.retrieveValue(Utils.USER_PROFILE_JSON);
        return this.getJsonValue("vPhone", profileData);
    }


    public void showMessageDialog(String s){

        LayoutInflater inflater = ((Activity) a).getLayoutInflater();
        View layout = inflater.inflate(R.layout.toast_main, (ViewGroup) ((Activity) a).findViewById(R.id.toast_layout_root));

        ImageView image = (ImageView) layout.findViewById(R.id.image);
        TextView text = (TextView) layout.findViewById(R.id.text);
        text.setText(s);

        Toast toast = new Toast(a);
        toast.setGravity(Gravity.TOP| Gravity.FILL_HORIZONTAL, 0, 0);
        toast.setDuration(Toast.LENGTH_SHORT);
        toast.setView(layout);
        toast.show();

        //Toast.makeText(mContext, s, Toast.LENGTH_SHORT).show();
    }


    public boolean isEmailVerified() {

        String profileData = this.retrieveValue(Utils.USER_PROFILE_JSON);
        return this.getJsonValue("eEmailVerified", profileData).equalsIgnoreCase("Yes") ? true : false;
    }

    public String getUserName() {

        String profileData = this.retrieveValue(Utils.USER_PROFILE_JSON);
        return ("@"+this.getJsonValue("vName", profileData)+""+this.getJsonValue("vLastName", profileData)).toLowerCase(Locale.ROOT).replace(" ", "");
    }

    public boolean isReferralSchemeEnable() {
        return !this.retrieveValue(Utils.REFERRAL_SCHEME_ENABLE).equals("") && this.retrieveValue(Utils.REFERRAL_SCHEME_ENABLE).equalsIgnoreCase("Yes");
    }

//    public void logOutUser(Object calleeObj) {
//        if (calleeObj != null && calleeObj.getClass().getSimpleName().equalsIgnoreCase("MyApp")) {
//            this.removeValue(Utils.iMemberId);
//            this.removeValue(Utils.isUserLogIn);
//            this.removeValue(Utils.DEFAULT_CURRENCY_VALUE);
//            this.removeValue("User_Profile");
//            this.removeValue("DeliverList");
//            this.removeValue(Utils.WORKLOCATION);
//            this.removeValue(Utils.SELECT_ADDRESS_ID);
//            this.removeValue("DEFAULT_SERVICE_CATEGORY_ID");
//            this.removeValue("DeliveryListMultiJson");
//            this.removeValue("DeliveryListAll");
//            this.removeValue("DeliveryListJSonMulti");
//            this.removeValue("userHomeLocationLatitude");
//            this.removeValue("userHomeLocationLongitude");
//            this.removeValue("userHomeLocationAddress");
//            this.removeValue("userWorkLocationLatitude");
//            this.removeValue("userWorkLocationLongitude");
//            this.removeValue("userWorkLocationAddress");
//
//            Realm var2;
//            try {
//                var2 = Realm.getInstance((new Builder()).deleteRealmIfMigrationNeeded().build());
//                var2.beginTransaction();
//                var2.deleteAll();
//                var2.commitTransaction();
//            } catch (Exception var4) {
//                System.out.println("RealmDeleteError:" + var4.getMessage());
//            }
//
//            try {
//                var2 = Realm.getDefaultInstance();
//                var2.beginTransaction();
//                var2.deleteAll();
//                var2.commitTransaction();
//            } catch (Exception var3) {
//                System.out.println("RealmDeleteError:" + var3.getMessage());
//            }
//        } else {
//            System.out.println("LogOutError:This method should not be used from anywhere. If you want to logout from device use method (logOutFromDevice) of MyApp:");
//        }
//
//    }

    public void setWindowFlag(Activity activity, final int bits, boolean on) {
        Window win = activity.getWindow();
        WindowManager.LayoutParams winParams = win.getAttributes();
        if (on) {
            winParams.flags |= bits;
        } else {
            winParams.flags &= ~bits;
        }
        win.setAttributes(winParams);
    }


    public boolean isUserLoggedIn() {
        SharedPreferences var1 = PreferenceManager.getDefaultSharedPreferences(this.a);
        String var2 = var1.getString(Utils.isUserLogIn, "");
        String var3 = var1.getString(Utils.iMemberId, "");
        return !var2.equals("") && var2.equals("1") && !var3.trim().equals("");
    }

    public boolean isLanguageLabelsAvail() {
        SharedPreferences var1 = PreferenceManager.getDefaultSharedPreferences(this.a);
        String var2 = var1.getString(Utils.languageLabelsKey, (String)null);
        return var2 != null && !var2.equals("");
    }

    public Object getValueFromJsonArr(JSONArray arr, int position) {
        try {
            return arr.get(position);
        } catch (JSONException var4) {
            var4.printStackTrace();
            return "";
        }
    }

    public String getJsonValue(String key, String response) {
        try {
            JSONObject var3 = new JSONObject(response);
            if (!var3.isNull(key)) {
                String var4 = var3.getString(key);
                if (var4 != null && !var4.equals("null") && !var4.equals("")) {
                    return var4;
                }
            }

            return "";
        } catch (JSONException var5) {
            return "";
        }
    }

    public JSONArray getJsonArray(String key, String response) {
        try {
            JSONObject var3 = new JSONObject(response);
            JSONArray var4 = var3.getJSONArray(key);
            return var4;
        } catch (JSONException var5) {
            return null;
        }
    }

    public JSONArray getJsonArray(String key, JSONObject obj) {
        try {
            JSONArray var3 = obj.getJSONArray(key);
            return var3;
        } catch (JSONException var4) {
            return null;
        }
    }

    public JSONArray getJsonArray(String response) {
        try {
            JSONArray var2 = new JSONArray(response);
            return var2;
        } catch (JSONException var3) {
            return null;
        }
    }

    public JSONObject getJsonObject(JSONArray arr, int position) {
        try {
            JSONObject var3 = arr.getJSONObject(position);
            return var3;
        } catch (JSONException var4) {
            return null;
        }
    }

    public Object getJsonValue(JSONArray arr, int position) {
        try {
            Object var3 = arr.get(position);
            return var3;
        } catch (JSONException var4) {
            return null;
        }
    }

    public JSONObject getJsonObject(String key, JSONObject obj) {
        try {
            JSONObject var3 = obj.getJSONObject(key);
            return var3;
        } catch (JSONException var4) {
            return null;
        }
    }

    public JSONObject getJsonObject(String data) {
        try {
            JSONObject var2 = new JSONObject(data);
            return var2;
        } catch (JSONException var3) {
            return null;
        }
    }

    public JSONObject getJsonObject(String key, String response) {
        try {
            JSONObject var3 = new JSONObject(response);
            JSONObject var4 = var3.getJSONObject(key);
            return var4 != null && !var4.equals("null") && !var4.equals("") ? var4 : null;
        } catch (JSONException var5) {
            return null;
        }
    }

    public boolean isJSONkeyAvail(String key, String response) {
        try {
            JSONObject var3 = new JSONObject(response);
            return var3.has(key) && !var3.isNull(key);
        } catch (JSONException var4) {
            return false;
        }
    }

    public boolean isJSONArrKeyAvail(String key, String response) {
        try {
            JSONObject var3 = new JSONObject(response);
            return var3.optJSONArray(key) != null;
        } catch (JSONException var4) {
            return false;
        }
    }

    public void sendHeartBeat() {
        this.a.sendBroadcast(new Intent("com.google.android.intent.action.GTALK_HEARTBEAT"));
        this.a.sendBroadcast(new Intent("com.google.android.intent.action.MCS_HEARTBEAT"));
    }

    public boolean isEmailValid(String email) {
        boolean var2 = false;
        String var3 = "[A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,20}";
        String var4 = email.trim();
        Pattern var5 = Pattern.compile(var3, 2);
        Matcher var6 = var5.matcher(var4);
        if (var6.matches()) {
            var2 = true;
        }

        return var2;
    }

//    public void generateErrorView(ErrorView errorView, String title, String subTitle) {
//        errorView.setConfig(Config.create().title("").titleColor(this.a.getResources().getColor(17170444)).subtitle(this.retrieveLangLBl("", subTitle)).retryText(this.retrieveLangLBl("Retry", "LBL_RETRY_TXT")).retryTextColor(this.a.getResources().getColor(color.error_view_retry_btn_txt_color_prj)).build());
//    }
//
//    public void showError() {
//        InternetConnection var1 = new InternetConnection(this.a);
//        String var2 = !var1.isNetworkConnected() && !var1.check_int() ? this.retrieveLangLBl("No Internet Connection", "LBL_NO_INTERNET_TXT") : this.retrieveLangLBl("Please try again.", "LBL_TRY_AGAIN_TXT");
//        GenerateAlertBox var3 = new GenerateAlertBox(this.a);
//        var3.setContentMessage("", var2);
//        var3.setPositiveBtn(this.retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
//        var3.showAlertBox();
//    }

//    public void showError(boolean isClose) {
//        InternetConnection var2 = new InternetConnection(this.a);
//        String var3 = !var2.isNetworkConnected() && !var2.check_int() ? this.retrieveLangLBl("No Internet Connection", "LBL_NO_INTERNET_TXT") : this.retrieveLangLBl("Please try again.", "LBL_TRY_AGAIN_TXT");
//        GenerateAlertBox var4 = new GenerateAlertBox(this.a);
//        var4.setContentMessage("", var3);
//        var4.setPositiveBtn(this.retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
//        var4.setBtnClickList((var2x) -> {
//            if (isClose && this.a instanceof Activity) {
//                ((Activity)this.a).onBackPressed();
//            }
//
//        });
//        var4.showAlertBox();
//    }
//
//    public void showError(GeneralFunctions.OnAlertButtonClickListener onAlertButtonClickListener) {
//        InternetConnection var2 = new InternetConnection(this.a);
//        String var3 = !var2.isNetworkConnected() && !var2.check_int() ? this.retrieveLangLBl("No Internet Connection", "LBL_NO_INTERNET_TXT") : this.retrieveLangLBl("Please try again.", "LBL_TRY_AGAIN_TXT");
//        GenerateAlertBox var4 = new GenerateAlertBox(this.a);
//        var4.setContentMessage("", var3);
//        var4.setBtnClickList((var2x) -> {
//            var4.closeAlertBox();
//            if (onAlertButtonClickListener != null) {
//                onAlertButtonClickListener.onAlertButtonClick(var2x);
//            }
//
//        });
//        var4.setPositiveBtn(this.retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
//        var4.showAlertBox();
//    }
//
//    public GenerateAlertBox showGeneralMessage(String title, String message) {
//        try {
//            GenerateAlertBox var3 = new GenerateAlertBox(this.a);
//            var3.setContentMessage(title, message);
//            var3.setPositiveBtn(this.retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
//            var3.showAlertBox();
//            return var3;
//        } catch (Exception var4) {
//            return null;
//        }
//    }
//
//    public void showGeneralMessage(String title, String message, GeneralFunctions.OnAlertButtonClickListener onAlertButtonClickListener) {
//        try {
//            GenerateAlertBox var4 = new GenerateAlertBox(this.a);
//            var4.setContentMessage(title, message);
//            var4.setPositiveBtn(this.retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
//            var4.setBtnClickList((var2) -> {
//                var4.closeAlertBox();
//                if (onAlertButtonClickListener != null) {
//                    onAlertButtonClickListener.onAlertButtonClick(var2);
//                }
//
//            });
//            var4.showAlertBox();
//        } catch (Exception var5) {
//        }
//
//    }
//
//    public void showGeneralMessage(String title, String message, boolean isCloseScreen) {
//        try {
//            GenerateAlertBox var4 = new GenerateAlertBox(this.a);
//            var4.setContentMessage(title, message);
//            var4.setPositiveBtn(this.retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
//            var4.setBtnClickList((var3) -> {
//                var4.closeAlertBox();
//                if (this.a instanceof Activity && isCloseScreen) {
//                    ((Activity)this.a).onBackPressed();
//                }
//
//            });
//            var4.showAlertBox();
//        } catch (Exception var5) {
//        }
//
//    }
//
//    public GenerateAlertBox showGeneralMessage(String title, String message, String negativeButton, String positiveButton, GeneralFunctions.OnAlertButtonClickListener onAlertButtonClickListener) {
//        try {
//            GenerateAlertBox var6 = new GenerateAlertBox(this.a);
//            var6.setContentMessage(title, message);
//            var6.setNegativeBtn(negativeButton);
//            var6.setPositiveBtn(positiveButton);
//            var6.setBtnClickList((var2) -> {
//                var6.closeAlertBox();
//                if (onAlertButtonClickListener != null) {
//                    onAlertButtonClickListener.onAlertButtonClick(var2);
//                }
//
//            });
//            var6.showAlertBox();
//            return var6;
//        } catch (Exception var7) {
//            return null;
//        }
//    }

    public boolean isLocationEnabled() {
        int var1 = 0;
        if (Build.VERSION.SDK_INT >= 19) {
            try {
                var1 = Settings.Secure.getInt(this.a.getContentResolver(), "location_mode");
            } catch (Settings.SettingNotFoundException var5) {
                var5.printStackTrace();
            }

            @SuppressLint("WrongConstant") LocationManager var3 = (LocationManager)this.a.getSystemService("location");
            boolean var4 = var3.isProviderEnabled("gps");
            return var1 != 0 && var4;
        } else {
            String var2 = Settings.Secure.getString(this.a.getContentResolver(), "location_providers_allowed");
            return !TextUtils.isEmpty(var2);
        }
    }

    public boolean checkLocationPermission(boolean isPermissionDialogShown) {
        int var2 = ContextCompat.checkSelfPermission(this.a, "android.permission.ACCESS_FINE_LOCATION");
        int var3 = ContextCompat.checkSelfPermission(this.a, "android.permission.ACCESS_COARSE_LOCATION");
        if (var2 == 0 && var3 == 0) {
            return true;
        } else {
            if (!isPermissionDialogShown) {
                if (!(this.a instanceof Activity)) {
                    System.out.println("Context must be an instance of an activity.");
                } else {
                    ActivityCompat.requestPermissions((Activity)this.a, new String[]{"android.permission.ACCESS_FINE_LOCATION", "android.permission.ACCESS_COARSE_LOCATION"}, 51);
                }
            }

            return false;
        }
    }

    public boolean checkLocationPermission(Context mContext, boolean isPermissionDialogShown) {
        int var3 = ContextCompat.checkSelfPermission(mContext, "android.permission.ACCESS_FINE_LOCATION");
        int var4 = ContextCompat.checkSelfPermission(mContext, "android.permission.ACCESS_COARSE_LOCATION");
        if (var3 == 0 && var4 == 0) {
            return true;
        } else {
            if (!isPermissionDialogShown) {
                if (!(mContext instanceof Activity)) {
                    System.out.println("Context must be an instance of an activity.");
                } else {
                    ActivityCompat.requestPermissions((Activity)mContext, new String[]{"android.permission.ACCESS_FINE_LOCATION", "android.permission.ACCESS_COARSE_LOCATION"}, 51);
                }
            }

            return false;
        }
    }

    public boolean isStoragePermissionGranted() {
        if (Build.VERSION.SDK_INT >= 23) {
            if (ContextCompat.checkSelfPermission(this.a, "android.permission.WRITE_EXTERNAL_STORAGE") == 0) {
                return true;
            } else {
                if (this.a instanceof Activity) {
                    ActivityCompat.requestPermissions((Activity)this.a, new String[]{"android.permission.WRITE_EXTERNAL_STORAGE"}, 51);
                }

                return false;
            }
        } else {
            return true;
        }
    }

    public boolean isCameraPermissionGranted() {
        if (Build.VERSION.SDK_INT >= 23) {
            if (ContextCompat.checkSelfPermission(this.a, "android.permission.CAMERA") == 0) {
                return true;
            } else {
                if (this.a instanceof Activity) {
                    ActivityCompat.requestPermissions((Activity)this.a, new String[]{"android.permission.CAMERA"}, 51);
                }

                return false;
            }
        } else {
            return true;
        }
    }

    public boolean isCallPermissionGranted(boolean openDialog) {
        int var2 = ContextCompat.checkSelfPermission(this.a, "android.permission.RECORD_AUDIO");
        int var3 = ContextCompat.checkSelfPermission(this.a, "android.permission.READ_PHONE_STATE");
        if (var2 == 0 && var3 == 0) {
            return true;
        } else {
            if (openDialog) {
                if (!(this.a instanceof Activity)) {
                    System.out.println("Context must be an instance of an activity.");
                } else {
                    ActivityCompat.requestPermissions((Activity)this.a, new String[]{"android.permission.READ_PHONE_STATE", "android.permission.RECORD_AUDIO"}, 51);
                }
            }

            return false;
        }
    }

    public boolean isLocationPermissionGranted(boolean openDialog) {
        int var2 = ContextCompat.checkSelfPermission(this.a, "android.permission.ACCESS_FINE_LOCATION");
        int var3 = ContextCompat.checkSelfPermission(this.a, "android.permission.ACCESS_COARSE_LOCATION");
        int var4 = ContextCompat.checkSelfPermission(this.a, "android.permission.WRITE_EXTERNAL_STORAGE");
        int var5 = ContextCompat.checkSelfPermission(this.a, "android.permission.CAMERA");
        if (var2 == 0 && var3 == 0) {
            return true;
        } else {
            if (openDialog) {
                if (!(this.a instanceof Activity)) {
                    System.out.println("Context must be an instance of an activity.");
                } else {
                    ActivityCompat.requestPermissions((Activity)this.a, new String[]{"android.permission.ACCESS_FINE_LOCATION", "android.permission.ACCESS_COARSE_LOCATION"}, 51);
                }
            }

            return false;
        }
    }

    public String[] generateImageParams(String key, String content) {
        String[] var3 = new String[]{key, content};
        return var3;
    }

    public boolean isAllPermissionGranted(boolean openDialog) {
        int var2 = ContextCompat.checkSelfPermission(this.a, "android.permission.ACCESS_FINE_LOCATION");
        int var3 = ContextCompat.checkSelfPermission(this.a, "android.permission.ACCESS_COARSE_LOCATION");
        int var4 = ContextCompat.checkSelfPermission(this.a, "android.permission.WRITE_EXTERNAL_STORAGE");
        int var5 = ContextCompat.checkSelfPermission(this.a, "android.permission.CAMERA");
        if (var2 == 0 && var3 == 0) {
            return true;
        } else {
            if (openDialog) {
                if (!(this.a instanceof Activity)) {
                    System.out.println("Context must be an instance of an activity.");
                } else {
                    ActivityCompat.requestPermissions((Activity)this.a, new String[]{"android.permission.ACCESS_FINE_LOCATION", "android.permission.ACCESS_COARSE_LOCATION"}, 51);
                }
            }

            return false;
        }
    }

    public boolean isCameraStoragePermissionGranted() {
        int var1 = ContextCompat.checkSelfPermission(this.a, "android.permission.WRITE_EXTERNAL_STORAGE");
        int var2 = ContextCompat.checkSelfPermission(this.a, "android.permission.CAMERA");
        if (var1 == 0 && var2 == 0) {
            return true;
        } else {
            if (!(this.a instanceof Activity)) {
                System.out.println("Context must be an instance of an activity.");
            } else {
                ActivityCompat.requestPermissions((Activity)this.a, new String[]{"android.permission.CAMERA", "android.permission.WRITE_EXTERNAL_STORAGE"}, 51);
            }

            return false;
        }
    }

    public void openSettings() {
        if (this.a instanceof Activity) {
            Utils.hideKeyboard((Activity)this.a);
            Intent var1 = new Intent("android.settings.APPLICATION_DETAILS_SETTINGS");
            Uri var2 = Uri.fromParts("package", this.a.getApplicationInfo().packageName, (String)null);
            var1.setData(var2);
            ((Activity)this.a).startActivityForResult(var1, 52);
        }

    }

//    public GenerateAlertBox notifyRestartApp() {
//        GenerateAlertBox var1 = new GenerateAlertBox(this.a);
//        var1.setContentMessage(this.retrieveLangLBl("", "LBL_BTN_TRIP_CANCEL_CONFIRM_TXT"), this.retrieveLangLBl("In order to apply changes restarting app is required. Please wait.", "LBL_NOTIFY_RESTART_APP_TO_CHANGE"));
//        var1.setPositiveBtn(this.retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
//        var1.showAlertBox();
//        return var1;
//    }
//
//    public GenerateAlertBox notifyRestartApp(String title, String contentMsg) {
//        GenerateAlertBox var3 = new GenerateAlertBox(this.a);
//        var3.setContentMessage(title, contentMsg);
//        var3.setPositiveBtn(this.retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
//        var3.showAlertBox();
//        return var3;
//    }

    public void restartApp() {
        (new StartActProcess(this.a)).startAct(Utils.getLauncherIntent(this.a));
        ((Activity)this.a).setResult(0);

        try {
            ActivityCompat.finishAffinity((Activity)this.a);
        } catch (Exception var2) {
        }

        Utils.runGC();
    }


//    public void showMessage(View view, String message) {
//        Snackbar var3 = Snackbar.make(view, message, 0);
//        View var4 = var3.getView();
//        TextView var5 = (TextView)var4.findViewById(snackbar);
//        var5.setMaxLines(5);.
//        var3.show();
//    }

    public void doBounceAnimation(View targetView) {
        Interpolator interpolator = new Interpolator() {
            @Override
            public float getInterpolation(float v) {
                return getPowOut(v,1);//Add getPowOut(v,3); for more up animation
            }
        };
        ObjectAnimator animator = ObjectAnimator.ofFloat(targetView, "translationY", 0, -25, 0);
        animator.setInterpolator(interpolator);
//        animator.setStartDelay(200);
        animator.setDuration(800);
        animator.setRepeatCount(0);
        animator.start();
    }

    private float getPowIn(float elapsedTimeRate, double pow) {
        return (float) Math.pow(elapsedTimeRate, pow);
    }

    private float getPowOut(float elapsedTimeRate, double pow) {
        return (float) ((float) 1 - Math.pow(1 - elapsedTimeRate, pow));
    }

    public void showMessage( String message) {
        Toast.makeText(a,message, Toast.LENGTH_SHORT).show();
    }

    public void displayLog( String message) {
        Log.d(a.getClass().getSimpleName(), message);
    }
    public void displayLog(String tag, String message) {
        Log.d(tag, message);
    }

    public String getSelectedCarTypeData(String selectedCarTypeId, String jsonArrKey, String dataKey, String json) {
        JSONArray var5 = this.getJsonArray(jsonArrKey, json);

        for(int var6 = 0; var6 < var5.length(); ++var6) {
            JSONObject var7 = this.getJsonObject(var5, var6);
            String var8 = this.getJsonValueStr("iVehicleTypeId", var7);
            if (var8.equals(selectedCarTypeId)) {
                String var9 = this.getJsonValue(dataKey, var7.toString());
                return var9;
            }
        }

        return "";
    }

    public String getSelectedCarTypeData(String selectedCarTypeId, ArrayList<HashMap<String, String>> dataList, String dataKey) {
        for(int var4 = 0; var4 < dataList.size(); ++var4) {
            HashMap var5 = (HashMap)dataList.get(var4);
            String var6 = (String)var5.get("iVehicleTypeId");
            if (var6.equals(selectedCarTypeId)) {
                String var7 = (String)var5.get(dataKey);
                return var7;
            }
        }

        return "";
    }

    public void deleteTripStatusMessages() {
        SharedPreferences var1 = PreferenceManager.getDefaultSharedPreferences(this.a);
        Map var2 = var1.getAll();
        Iterator var3 = var2.entrySet().iterator();

        while(var3.hasNext()) {
            Map.Entry var4 = (Map.Entry)var3.next();
            if (((String)var4.getKey()).contains("TRIP_STATUS_MSG_")) {
                Long var5 = System.currentTimeMillis() - 86400000L;
                long var6 = parseLongValue(0L, var4.getValue().toString());
                if (var5 >= var6) {
                    this.removeValue((String)var4.getKey());
                }
            }
        }

    }

//    public PolylineOptions getGoogleRouteOptions(String directionJson, int width, int color) {
//        PolylineOptions var4 = new PolylineOptions();
//
//        try {
//            DirectionsJSONParser var5 = new DirectionsJSONParser();
//            List var6 = var5.parse(new JSONObject(directionJson));
//            ArrayList var7 = new ArrayList();
//            if (var6.size() <= 0) {
//                return null;
//            } else {
//                List var8 = (List)var6.get(0);
//
//                for(int var9 = 0; var9 < var8.size(); ++var9) {
//                    HashMap var10 = (HashMap)var8.get(var9);
//                    double var11 = Double.parseDouble((String)var10.get("lat"));
//                    double var13 = Double.parseDouble((String)var10.get("lng"));
//                    LatLng var15 = new LatLng(var11, var13);
//                    var7.add(var15);
//                }
//
//                var4.addAll(var7);
//                var4.width((float)width);
//                var4.color(color);
//                return var4;
//            }
//        } catch (Exception var16) {
//            return null;
//        }
//    }

//    public void verifyMobile(Bundle bn, Fragment fragment, Class classToOpen) {
//        GenerateAlertBox var4 = new GenerateAlertBox(this.a);
//        var4.setCancelable(false);
//        var4.setBtnClickList((var5) -> {
//            var4.closeAlertBox();
//            if (var5 != 0) {
//                if (fragment == null) {
//                    (new StartActProcess(this.a)).startActForResult(classToOpen, bn, 52);
//                } else {
//                    (new StartActProcess(this.a)).startActForResult(fragment, classToOpen, 52, bn);
//                }
//
//            }
//        });
//        var4.setContentMessage("", this.retrieveLangLBl("", "LBL_VERIFY_MOBILE_CONFIRM_MSG"));
//        var4.setPositiveBtn(this.retrieveLangLBl("", "LBL_BTN_OK_TXT"));
//        var4.setNegativeBtn(this.retrieveLangLBl("", "LBL_CANCEL_TXT"));
//        var4.showAlertBox();
//    }

    public boolean canDrawOverlayViews(Context con) {
        if (Build.VERSION.SDK_INT < 21) {
            return true;
        } else {
            try {
                return Build.VERSION.SDK_INT >= 23 ? Settings.canDrawOverlays(con) : canDrawOverlaysUsingReflection(con);
            } catch (NoSuchMethodError var3) {
                return canDrawOverlaysUsingReflection(con);
            }
        }
    }

//    public String decodeFile(String path, int DESIREDWIDTH, int DESIREDHEIGHT, String tempImgName) {
//        String var5 = null;
//        Bitmap var6 = null;
//
//        try {
//            int var7 = Utils.getExifRotation(path);
//            Bitmap var8 = ScalingUtilities.decodeFile(path, DESIREDWIDTH, DESIREDHEIGHT, ScalingLogic.CROP);
//            if (var8.getWidth() <= DESIREDWIDTH && var8.getHeight() <= DESIREDHEIGHT) {
//                if (var8.getWidth() > var8.getHeight()) {
//                    var6 = ScalingUtilities.createScaledBitmap(var8, var8.getHeight(), var8.getHeight(), ScalingLogic.CROP);
//                } else {
//                    var6 = ScalingUtilities.createScaledBitmap(var8, var8.getWidth(), var8.getWidth(), ScalingLogic.CROP);
//                }
//            } else {
//                var6 = ScalingUtilities.createScaledBitmap(var8, DESIREDWIDTH, DESIREDHEIGHT, ScalingLogic.CROP);
//            }
//
//            var6 = rotateBitmap(var6, var7);
//            String var9 = Environment.getExternalStorageDirectory().toString();
//            File var10 = new File(var9 + "/" + "TempImages");
//            if (!var10.exists()) {
//                var10.mkdir();
//            }
//
//            File var11 = new File(var10.getAbsolutePath(), tempImgName);
//            var5 = var11.getAbsolutePath();
//            FileOutputStream var12 = null;
//
//            try {
//                var12 = new FileOutputStream(var11);
//                var6.compress(Bitmap.CompressFormat.JPEG, 60, var12);
//                var12.flush();
//                var12.close();
//            } catch (FileNotFoundException var14) {
//                var14.printStackTrace();
//            } catch (Exception var15) {
//                var15.printStackTrace();
//            }
//
//            var6.recycle();
//        } catch (Throwable var16) {
//        }
//
//        return var5 == null ? path : var5;
//    }

    public boolean isServiceRunning(Class<?> serviceClass) {
        @SuppressLint("WrongConstant") ActivityManager var2 = (ActivityManager)this.a.getSystemService("activity");
        Iterator var3 = var2.getRunningServices(2147483647).iterator();

        ActivityManager.RunningServiceInfo var4;
        do {
            if (!var3.hasNext()) {
                return false;
            }

            var4 = (ActivityManager.RunningServiceInfo)var3.next();
        } while(!serviceClass.getName().equals(var4.service.getClassName()));

        return true;
    }

    public Bitmap writeTextOnDrawable(Context mContext, int drawableId, String text, boolean iswhite, int fontPathToLoad) {
        Bitmap var6 = BitmapFactory.decodeResource(mContext.getResources(), drawableId).copy(android.graphics.Bitmap.Config.ARGB_8888, true);
        Typeface var7 = Typeface.createFromAsset(mContext.getAssets(), mContext.getResources().getString(fontPathToLoad));
        Paint var8 = new Paint();
        var8.setStyle(Paint.Style.FILL);
        if (iswhite) {
            var8.setColor(-1);
        } else {
            var8.setColor(-16777216);
        }

        var8.setTypeface(var7);
        var8.setTextAlign(Paint.Align.CENTER);
        var8.setTextSize((float)Utils.dipToPixels(mContext, 14.0F));
        Rect var9 = new Rect();
        var8.getTextBounds(text, 0, text.length(), var9);
        Canvas var10 = new Canvas(var6);
        if (var9.width() >= var10.getWidth() - 4) {
            var8.setTextSize((float)Utils.dipToPixels(mContext, 14.0F));
        }

        int var11 = var10.getWidth() / 2 - 2;
        int var12 = (int)((float)(var10.getHeight() / 4) - (var8.descent() + var8.ascent()) / 2.0F);
        String[] var13 = text.split("\n");
        int var14 = var13.length;

        for(int var15 = 0; var15 < var14; ++var15) {
            String var16 = var13[var15];
            var10.drawText(var16, (float)var11, (float)var12, var8);
            var8.setTextSize((float)Utils.dipToPixels(mContext, 14.0F));
            var12 = (int)((float)var12 + (var8.descent() - var8.ascent()));
        }

        return var6;
    }

//    public void makeTextViewResizable(final TextView tv, final int maxLine, final String expandText, final boolean viewMore, final int color, final int dimension, final GeneralFunctions.ResizableTexViewClickListener listener) {
//        if (tv.getTag() == null) {
//            tv.setTag(tv.getText());
//        }
//
//        ViewTreeObserver var8 = tv.getViewTreeObserver();
//        var8.addOnGlobalLayoutListener(new ViewTreeObserver.OnGlobalLayoutListener() {
//            public void onGlobalLayout() {
//                ViewTreeObserver var1 = tv.getViewTreeObserver();
//                var1.removeGlobalOnLayoutListener(this);
//                int var2;
//                String var3;
//                if (maxLine == 0) {
//                    var2 = tv.getLayout().getLineEnd(0);
//                    var3 = tv.getText().subSequence(0, var2 - expandText.length() + 1) + " " + expandText;
//                    tv.setText(var3);
//                    tv.setMovementMethod(LinkMovementMethod.getInstance());
//                    tv.setText(GeneralFunctions.this.a(tv.getText().toString(), tv, maxLine, expandText, viewMore, color, dimension, listener), TextView.BufferType.SPANNABLE);
//                } else if (maxLine > 0 && tv.getLineCount() >= maxLine) {
//                    var2 = tv.getLayout().getLineEnd(maxLine - 1);
//                    var3 = tv.getText().subSequence(0, var2 - expandText.length() + 1) + " " + expandText;
//                    tv.setText(var3);
//                    tv.setMovementMethod(LinkMovementMethod.getInstance());
//                    tv.setText(GeneralFunctions.this.a(tv.getText().toString(), tv, maxLine, expandText, viewMore, color, dimension, listener), TextView.BufferType.SPANNABLE);
//                } else if (tv.getLineCount() > maxLine) {
//                    var2 = tv.getLayout().getLineEnd(tv.getLayout().getLineCount() - 1);
//                    var3 = tv.getText().subSequence(0, var2) + " " + expandText;
//                    tv.setText(var3);
//                    tv.setMovementMethod(LinkMovementMethod.getInstance());
//                    tv.setText(GeneralFunctions.this.a(tv.getText().toString(), tv, var2, expandText, viewMore, color, dimension, listener), TextView.BufferType.SPANNABLE);
//                }
//
//            }
//        });
//    }

//    public void makeTextViewResizable(final TextView tv, final int maxLine, final String expandText, final boolean viewMore, final int color, final int dimension) {
//        if (tv.getTag() == null) {
//            tv.setTag(tv.getText());
//        }
//
//        ViewTreeObserver var7 = tv.getViewTreeObserver();
//        var7.addOnGlobalLayoutListener(new ViewTreeObserver.OnGlobalLayoutListener() {
//            public void onGlobalLayout() {
//                ViewTreeObserver var1 = tv.getViewTreeObserver();
//                var1.removeGlobalOnLayoutListener(this);
//                int var2;
//                String var3;
//                if (maxLine == 0) {
//                    var2 = tv.getLayout().getLineEnd(0);
//                    var3 = tv.getText().subSequence(0, var2 - expandText.length() + 1) + " " + expandText;
//                    tv.setText(var3);
//                    tv.setMovementMethod(LinkMovementMethod.getInstance());
//                    tv.setText(GeneralFunctions.this.a(tv.getText().toString(), tv, maxLine, expandText, viewMore, color, dimension, (GeneralFunctions.ResizableTexViewClickListener)null), TextView.BufferType.SPANNABLE);
//                } else if (maxLine > 0 && tv.getLineCount() >= maxLine) {
//                    var2 = tv.getLayout().getLineEnd(maxLine - 1);
//                    var3 = tv.getText().subSequence(0, var2 - expandText.length() + 1) + " " + expandText;
//                    tv.setText(var3);
//                    tv.setMovementMethod(LinkMovementMethod.getInstance());
//                    tv.setText(GeneralFunctions.this.a(tv.getText().toString(), tv, maxLine, expandText, viewMore, color, dimension, (GeneralFunctions.ResizableTexViewClickListener)null), TextView.BufferType.SPANNABLE);
//                } else if (tv.getLineCount() > maxLine) {
//                    var2 = tv.getLayout().getLineEnd(tv.getLayout().getLineCount() - 1);
//                    var3 = tv.getText().subSequence(0, var2) + " " + expandText;
//                    tv.setText(var3);
//                    tv.setMovementMethod(LinkMovementMethod.getInstance());
//                    tv.setText(GeneralFunctions.this.a(tv.getText().toString(), tv, var2, expandText, viewMore, color, dimension, (GeneralFunctions.ResizableTexViewClickListener)null), TextView.BufferType.SPANNABLE);
//                }
//
//            }
//        });
//    }

//    private SpannableStringBuilder a(String var1, final TextView var2, int var3, String var4, final boolean var5, int var6, int var7, final GeneralFunctions.ResizableTexViewClickListener var8) {
//        SpannableStringBuilder var9 = new SpannableStringBuilder(var1);
//        if (var1.contains(var4)) {
//            var9.setSpan(new MyClickableSpan(this.a, var6, var7) {
//                public void onClick(View widget) {
//                    if (var8 != null) {
//                        var8.onResizableTextViewClick(var5);
//                    } else {
//                        if (var5) {
//                            var2.setLayoutParams(var2.getLayoutParams());
//                            var2.setText(var2.getTag().toString(), TextView.BufferType.SPANNABLE);
//                            var2.invalidate();
//                            GeneralFunctions.this.makeTextViewResizable(var2, -5, "\n- " + GeneralFunctions.this.retrieveLangLBl("Less", "LBL_LESS_TXT"), false, this.f, this.g);
//                        } else {
//                            var2.setLayoutParams(var2.getLayoutParams());
//                            var2.setText(var2.getTag().toString(), TextView.BufferType.SPANNABLE);
//                            var2.invalidate();
//                            GeneralFunctions.this.makeTextViewResizable(var2, 5, "...\n+ " + GeneralFunctions.this.retrieveLangLBl("View More", "LBL_VIEW_MORE_TXT"), true, this.f, this.g);
//                        }
//
//                    }
//                }
//            }, var1.indexOf(var4), var1.indexOf(var4) + var4.length(), 0);
//        }
//
//        return var9;
//    }

    public void saveGoOnlineInfo() {
        this.storeData(Utils.GO_ONLINE_KEY, "Yes");
        this.storeData(Utils.LAST_FINISH_TRIP_TIME_KEY, "" + Calendar.getInstance().getTimeInMillis());
    }

    public String getLocationUpdateChannel() {
        return "ONLINE_DRIVER_LOC_" + this.getMemberId();
    }

    public String buildLocationJson(Location location) {
        if (location != null) {
            try {
                JSONObject var2 = new JSONObject();
                var2.put("MsgType", "LocationUpdate");
                var2.put("iDriverId", this.getMemberId());
                var2.put("vLatitude", location.getLatitude());
                var2.put("vLongitude", location.getLongitude());
                var2.put("ChannelName", this.getLocationUpdateChannel());
                var2.put("LocTime", System.currentTimeMillis() + "");
                return var2.toString();
            } catch (Exception var3) {
                return "";
            }
        } else {
            return "";
        }
    }

    public String buildLocationJson(Location location, String msgType) {
        if (location != null) {
            try {
                JSONObject var3 = new JSONObject();
                var3.put("MsgType", msgType);
                var3.put("iDriverId", this.getMemberId());
                var3.put("vLatitude", location.getLatitude());
                var3.put("vLongitude", location.getLongitude());
                var3.put("ChannelName", this.getLocationUpdateChannel());
                var3.put("LocTime", System.currentTimeMillis() + "");
                return var3.toString();
            } catch (Exception var4) {
                return "";
            }
        } else {
            return "";
        }
    }

    public String buildRequestCancelJson(String iUserId, String vMsgCode) {
        try {
            JSONObject var3 = new JSONObject();
            var3.put("MsgType", "TripRequestCancel");
            var3.put("Message", "TripRequestCancel");
            var3.put("iDriverId", this.getMemberId());
            var3.put("iUserId", iUserId);
            var3.put("iTripId", vMsgCode);
            return var3.toString();
        } catch (Exception var4) {
            return "";
        }
    }

    public String convertNumberWithRTL(String data) {
        String var2 = "";
        NumberFormat var3 = null;

        try {
            Locale var4 = new Locale(this.retrieveValue(Utils.LANGUAGE_CODE_KEY));
            var3 = NumberFormat.getInstance(var4);
            if (data != null && !data.equals("")) {
                for(int var5 = 0; var5 < data.length(); ++var5) {
                    char var6 = data.charAt(var5);
                    if (Character.isDigit(var6)) {
                        var2 = var2 + var3.format((long)Integer.parseInt(String.valueOf(var6)));
                    } else {
                        var2 = var2 + var6;
                    }
                }
            }

            return var2;
        } catch (Exception var7) {
            System.out.println("Exception umber: " + var7.toString());
            return var2;
        }
    }

    public String getCurrentdate() {
        Calendar var1 = Calendar.getInstance();
        SimpleDateFormat var2 = new SimpleDateFormat(Utils.dateFormateForBooking);
        String var3 = var2.format(var1.getTime());
        return var3;
    }

    public String formatUpto2Digit(float discount) {
        return "" + (double)Math.round((double)discount * 100.0D) / 100.0D;
    }

    public String formatUpto2Digit(double discount) {
        return "" + (double)Math.round(discount * 100.0D) / 100.0D;
    }

    public Bundle createChatBundle(JSONObject obj_msg) {
        Bundle var2 = new Bundle();
        var2.putString("iFromMemberId", this.getJsonValueStr("iFromMemberId", obj_msg));
        var2.putString("FromMemberImageName", this.getJsonValueStr("FromMemberImageName", obj_msg));
        var2.putString("iTripId", this.getJsonValueStr("iTripId", obj_msg));
        var2.putString("FromMemberName", this.getJsonValueStr("FromMemberName", obj_msg));
        var2.putString("vBookingNo", this.getJsonValueStr("vBookingNo", obj_msg));
        return var2;
    }

    private JSONArray a(JSONArray var1, JSONArray var2) throws JSONException {
        JSONArray var3 = new JSONArray();

        int var4;
        for(var4 = 0; var4 < var1.length(); ++var4) {
            var3.put(var1.get(var4));
        }

        for(var4 = 0; var4 < var2.length(); ++var4) {
            var3.put(var2.get(var4));
        }

        return var3;
    }

    public boolean isDeliverOnlyEnabled() {
        return this.retrieveValue(Utils.ONLYDELIVERALL_KEY).equalsIgnoreCase("Yes");
    }

    public boolean isAnyDeliverOptionEnabled() {
        return this.retrieveValue(Utils.ONLYDELIVERALL_KEY).equalsIgnoreCase("Yes") || this.retrieveValue(Utils.DELIVERALL_KEY).equalsIgnoreCase("Yes");
    }

//    public void removeAllRealmData(Realm realm) {
//        realm.beginTransaction();
//        realm.deleteAll();
//        realm.commitTransaction();
//    }

    public boolean prefHasKey(String key) {
        SharedPreferences var2 = PreferenceManager.getDefaultSharedPreferences(this.a);
        return var2.contains(key);
    }

    public String getServiceId() {
        return this.retrieveValue(Utils.iServiceId_KEY);
    }

    public void resetMultiStoredDetails() {
        if (this.prefHasKey("DeliveryListMultiJson")) {
            this.removeValue("DeliveryListMultiJson");
        }

        if (this.prefHasKey("DeliveryListAll")) {
            this.removeValue("DeliveryListAll");
        }

        if (this.prefHasKey("DeliveryListJSonMulti")) {
            this.removeValue("DeliveryListJSonMulti");
        }

    }

    public JSONArray getJsonArray(JSONArray arr, int position) {
        try {
            JSONArray var3 = arr.getJSONArray(position);
            return var3;
        } catch (JSONException var4) {
            return null;
        }
    }

    public String getCurrentDayName() {
        Calendar var1 = Calendar.getInstance();
        new SimpleDateFormat(Utils.dateFormateForBooking);
        return (String) DateFormat.format("EEEE", var1.getTime());
    }

    public String getCurrentDateHourMin() {
        Calendar var1 = Calendar.getInstance();
        SimpleDateFormat var2 = new SimpleDateFormat(Utils.OriginalDateFormate);
        String var3 = var2.format(var1.getTime());
        return var3;
    }

    public interface OnAlertButtonClickListener {
        void onAlertButtonClick(int var1);
    }

    public interface ResizableTexViewClickListener {
        void onResizableTextViewClick(boolean var1);
    }

    public Bitmap resizeMarkerUser(int resource, int h, int w) {

        int height = h;
        int width = w;
        BitmapDrawable bitmapdraw = (BitmapDrawable) a.getResources().getDrawable(resource);
        Bitmap b = bitmapdraw.getBitmap();
        Bitmap smallMarker = Bitmap.createScaledBitmap(b, width, height, false);
        return smallMarker;
    }

    public String getDecimalWithSymbol(String value){

        Locale locale = new Locale("en","PH");
        DecimalFormat decimalFormat = (DecimalFormat) DecimalFormat.getCurrencyInstance(locale);
        DecimalFormatSymbols dfs = DecimalFormatSymbols.getInstance(locale);
        dfs.setCurrencySymbol("\u20B1");
        decimalFormat.setDecimalFormatSymbols(dfs);

        return decimalFormat.format(Double.parseDouble(value));
    }

    public String formatDate(String stringDate){


        String pattern1 = "yyyy-MM-dd HH:mm:ss";
        SimpleDateFormat simpleDateFormat1 = new SimpleDateFormat(pattern1);

        Date date = null;
        try {
            date = simpleDateFormat1.parse(stringDate);

            String pattern2 = "MM dd yyyy";
            SimpleDateFormat simpleDateFormat2 = new SimpleDateFormat(pattern2);

            String newdate = simpleDateFormat2.format(date);

            return  newdate ;
        } catch (ParseException e) {
            e.printStackTrace();
            return null;
        }

    }

    public String formatDateTime(String stringDate){


        String pattern1 = "yyyy-MM-dd HH:mm:ss";
        SimpleDateFormat simpleDateFormat1 = new SimpleDateFormat(pattern1);

        Date date = null;
        try {
            date = simpleDateFormat1.parse(stringDate);

            String pattern2 = "MMM dd, hh:mm aaa";
            SimpleDateFormat simpleDateFormat2 = new SimpleDateFormat(pattern2);

            String newdate = simpleDateFormat2.format(date);

            return  newdate ;
        } catch (ParseException e) {
            e.printStackTrace();
            return null;
        }

    }

    public String getDecimalWithSymbol(Double value){
        Locale locale = new Locale("en","PH");
        DecimalFormat decimalFormat = (DecimalFormat) DecimalFormat.getCurrencyInstance(locale);
        DecimalFormatSymbols dfs = DecimalFormatSymbols.getInstance(locale);
        dfs.setCurrencySymbol("\u20B1");
        decimalFormat.setDecimalFormatSymbols(dfs);

        return decimalFormat.format(value);
    }

    public void logout(){
        this.storeData(Utils.isUserLogIn, "0");
        this.storeData(Utils.iMemberId, "");
        this.storeData(Utils.USER_PROFILE_JSON, "");
        this.storeData(Utils.USER_FIRST_LOGIN, "0");

        new StartActProcess(a).startActClearTop(LauncherActivity.class);
        ((Activity) a).finish();
    }


    public static void expand(final View v, int duration, int targetHeight) {

        int prevHeight  = v.getHeight();

        v.setVisibility(View.VISIBLE);
        ValueAnimator valueAnimator = ValueAnimator.ofInt(prevHeight, targetHeight);
        valueAnimator.addUpdateListener(new ValueAnimator.AnimatorUpdateListener() {
            @Override
            public void onAnimationUpdate(ValueAnimator animation) {
                v.getLayoutParams().height = (int) animation.getAnimatedValue();
                v.requestLayout();
            }
        });
        valueAnimator.setInterpolator(new DecelerateInterpolator());
        valueAnimator.setDuration(duration);
        valueAnimator.start();
    }

    public static void collapse(final View v, int duration, int targetHeight) {
        int prevHeight  = v.getHeight();
        ValueAnimator valueAnimator = ValueAnimator.ofInt(prevHeight, targetHeight);
        valueAnimator.setInterpolator(new DecelerateInterpolator());
        valueAnimator.addUpdateListener(new ValueAnimator.AnimatorUpdateListener() {
            @Override
            public void onAnimationUpdate(ValueAnimator animation) {
                v.getLayoutParams().height = (int) animation.getAnimatedValue();
                v.requestLayout();
            }
        });
        valueAnimator.setInterpolator(new DecelerateInterpolator());
        valueAnimator.setDuration(duration);
        valueAnimator.start();
    }

    public boolean openURL(String url, String packageName, String className) {
        try {
            Intent var4 = new Intent("android.intent.action.VIEW", Uri.parse(url));
            var4.setClassName(packageName, className);
            this.a.startActivity(var4);
            return true;
        } catch (Exception var5) {
            this.openURL(url);
            return false;
        }
    }

    public void openURL(String url) {
        try {
            CustomTabsIntent.Builder builder = new CustomTabsIntent.Builder();
            CustomTabsIntent customTabsIntent = builder.build();
            customTabsIntent.launchUrl(a, Uri.parse(url));

        } catch (Exception var5) {
            try {
                Intent var3 = new Intent("android.intent.action.VIEW", Uri.parse(url));
                this.a.startActivity(var3);
            } catch (Exception var4) {
            }
        }
    }

    public String capitalize(String str) {
        if(str == null || str.isEmpty()) {
            return str;
        }

        return str.substring(0, 1).toUpperCase() + str.substring(1);
    }

    public int getAppVersionCode(){
        PackageInfo pInfo = null;
        try {
            pInfo = a.getPackageManager().getPackageInfo(a.getPackageName(), 0);
            int versionCode = pInfo.versionCode;
            return versionCode;
        } catch (PackageManager.NameNotFoundException e) {
            e.printStackTrace();
            return 0;
        }

    }
}
