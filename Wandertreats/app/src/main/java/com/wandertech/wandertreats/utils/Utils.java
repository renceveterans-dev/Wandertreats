package com.wandertech.wandertreats.utils;

import android.annotation.SuppressLint;
import android.annotation.TargetApi;
import android.app.Activity;
import android.app.ActivityManager;
import android.app.NotificationManager;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.content.res.Configuration;
import android.content.res.Resources;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Canvas;
import android.graphics.drawable.Drawable;
import android.media.ExifInterface;
import android.media.Ringtone;
import android.media.RingtoneManager;
import android.net.Uri;
import android.os.Build;
import android.os.VibrationEffect;
import android.os.Vibrator;
import android.telephony.TelephonyManager;
import android.text.SpannableString;
import android.text.TextUtils;
import android.text.style.ForegroundColorSpan;
import android.util.DisplayMetrics;
import android.util.TypedValue;
import android.view.MenuItem;
import android.view.View;
import android.view.inputmethod.InputMethodManager;
import android.widget.EditText;
import android.widget.ImageView;

import androidx.appcompat.widget.AppCompatEditText;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.core.graphics.drawable.DrawableCompat;

import java.io.File;
import java.io.IOException;
import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Iterator;
import java.util.List;
import java.util.Locale;
import java.util.concurrent.atomic.AtomicInteger;

public class Utils {
    public static final int NOTIFICATION_ID = 11;
    public static final String pubNub_Update_Loc_Channel_Prefix = "ONLINE_DRIVER_LOC_";
    public static final String deviceType = "Android";
    public static final String REMAINING_TIME = "REMAINING_TIME";
    public static String userType = "";
    public static String USER_ID_KEY = "";
    public static String IS_APP_IN_DEBUG_MODE = "";
    public static String CALLTO = "";
    public static final int MENU_PROFILE = 1;
    public static final int MENU_VEHICLE = 2;
    public static final int MENU_RIDE_HISTORY = 3;
    public static final int MENU_BOOKINGS = 4;
    public static final int MENU_ABOUT_US = 5;
    public static final int MENU_PAYMENT = 6;
    public static final int MENU_CONTACT_US = 7;
    public static final int MENU_HELP = 8;
    public static final int MENU_EMERGENCY_CONTACT = 9;
    public static final int MENU_SIGN_OUT = 10;
    public static final int MENU_WALLET = 11;
    public static final int MENU_INVITE_FRIEND = 12;
    public static final int MENU_POLICY = 13;
    public static final int MENU_ONGOING_TRIPS = 14;
    public static final int MENU_SUPPORT = 15;
    public static final int MENU_YOUR_TRIPS = 16;
    public static final int MENU_ACCOUNT_VERIFY = 17;
    public static final int MENU_BUSINESS_PROFILE = 18;
    public static final int MENU_FEEDBACK = 19;
    public static final int MENU_MY_HEATVIEW = 20;
    public static final int MENU_YOUR_DOCUMENTS = 21;
    public static final int MENU_MANAGE_VEHICLES = 22;
    public static final int MENU_TRIP_STATISTICS = 23;
    public static final int MENU_WAY_BILL = 24;
    public static final int MENU_BANK_DETAIL = 25;
    public static final int MENU_SET_AVAILABILITY = 26;
    public static final int MENU_ORDER = 27;
    public static final int MENU_CART = 28;
    public static final int MENU_ORDER_STATISTICS = 29;
    public static final int MENU_FOOD = 30;
    public static final int MENU_EARNING = 31;
    public static final int MENU_RESTAURANT_SETTINGS = 32;
    public static final int MENU_NOTIFICATION = 33;
    public static final int MENU_MY_GALLERY = 34;
    public static final int PLACE_AUTOCOMPLETE_REQUEST_CODE = 41;
    public static final int OVERLAY_PERMISSION_REQ_CODE = 42;
    public static final int MY_SCAN_REQUEST_CODE = 43;
    public static final int REQ_VERIFY_CARD_PIN_CODE = 44;
    public static final int REQ_VERIFY_INSTANT_PAYMENT_CODE = 45;
    public static final int SELECT_COUNTRY_REQ_CODE = 46;
    public static final int SEARCH_PICKUP_LOC_REQ_CODE = 47;
    public static final int UBER_X_SEARCH_PICKUP_LOC_REQ_CODE = 48;
    public static final int SEARCH_DEST_LOC_REQ_CODE = 49;
    public static final int MY_PROFILE_REQ_CODE = 50;
    public static final int OTHER_AREA_CLICKED_CODE = 51;
    public static final int VERIFY_MOBILE_REQ_CODE = 52;
    public static final int ADD_HOME_LOC_REQ_CODE = 53;
    public static final int ADD_WORK_LOC_REQ_CODE = 54;
    public static final int CARD_PAYMENT_REQ_CODE = 55;
    public static final int VERIFY_INFO_REQ_CODE = 56;
    public static final int ADD_VEHICLE_REQ_CODE = 57;
    public static final int ADD_RECIPIENT_REQ_CODE = 58;
    public static final int SELECT_RECIPIENT_REQ_CODE = 59;
    public static final int SELECT_COUPON_REQ_CODE = 60;
    public static final int ASSIGN_DRIVER_CODE = 61;
    public static final int PLACE_CUSTOME_LOC_REQUEST_CODE = 62;
    public static final int REQUEST_CODE_GPS_ON = 65;
    public static final int REQUEST_CODE_NETWOEK_ON = 67;
    public static final int FILTER_REQ_CODE = 68;
    public static final int COUPON_REQ_CODE = 69;
    public static final int REQ_OMISE_CODE = 70;
    public static final int UPLOAD_DOC_REQ_CODE = 71;
    public static final int SCHEDULE_REQUEST_CODE = 73;
    public static final int SELECT_ORGANIZATION_CODE = 74;
    public static final int SELECT_ORGANIZATION_PAYMENT_CODE = 75;
    public static final int ADD_MAP_LOC_REQ_CODE = 76;
    public static final int LIVE_TRACK_REQUEST_CODE = 77;
    public static final int ADD_LOC_REQ_CODE = 79;
    public static final int ORDER_DETAIL_REQUEST_CODE = 83;
    public static final int ADD_TO_BASKET = 84;
    public static final int SOCIAL_LOGIN_REQ_CODE = 85;
    public static final int ORDER_DETAILS_REQ_CODE = 86;
    public static final int TRACK_ORDER_REQ_CODE = 87;
    public static final int PLAY_SERVICES_RESOLUTION_REQUEST = 9000;
    public static final int LOCATION_UPDATE_MIN_DISTANCE_IN_MITERS = 2;
    public static final int LOCATION_POST_MIN_DISTANCE_IN_MITERS = 5;
    public static final int NOTIFICATION_BACKGROUND_ID = 12;
    public static final float defaultZomLevel = 16.5F;
    public static final int minPasswordLength = 6;
    public static final int ImageUpload_DESIREDWIDTH = 1024;
    public static final int ImageUpload_DESIREDHEIGHT = 1024;
    public static final int ImageUpload_MINIMUM_WIDTH = 256;
    public static final int ImageUpload_MINIMUM_HEIGHT = 256;
    public static String LIVE_CHAT_LICENCE_NUMBER = "LIVE_CHAT_LICENCE_NUMBER";
    public static final String All = "All";
    public static final String Notificatons = "Notification";
    public static final String News = "News";
    public static final String CALLTODRIVER = "Driver";
    public static final String CALLTOPASSENGER = "Passenger";
    public static final String CALLTOSTORE = "STORE";
    public static final String TempImageFolderPath = "TempImages";
    public static final String TempProfileImageName = "temp_pic_img.png";
    public static final String TRIP_REQ_CODE_PREFIX_KEY = "TRIP_STATUS_MSG_";
    public static final String PUBNUB_PUB_KEY = "PUBNUB_PUBLISH_KEY";
    public static final String PUBNUB_SUB_KEY = "PUBNUB_SUBSCRIBE_KEY";
    public static final String PUBNUB_SEC_KEY = "PUBNUB_SECRET_KEY";
    public static final String ENABLE_PUBNUB_KEY = "ENABLE_PUBNUB";
    public static final String USER_PROFILE_JSON = "User_Profile";
    public static final String SINCH_APP_KEY = "SINCH_APP_KEY";
    public static final String SINCH_APP_SECRET_KEY = "SINCH_APP_SECRET_KEY";
    public static final String SINCH_APP_ENVIRONMENT_HOST = "SINCH_APP_ENVIRONMENT_HOST";
    public static String HANDICAP_ACCESSIBILITY_OPTION = "HANDICAP_ACCESSIBILITY_OPTION";
    public static String CHILD_SEAT_ACCESSIBILITY_OPTION = "CHILD_SEAT_ACCESSIBILITY_OPTION";
    public static String WHEEL_CHAIR_ACCESSIBILITY_OPTION = "WHEEL_CHAIR_ACCESSIBILITY_OPTION";
    private static final AtomicInteger a = new AtomicInteger(1);
    public static String SMS_BODY_KEY = "SMS_BODY";
    public static String SESSION_ID_KEY = "APP_SESSION_ID";
    public static String DEVICE_SESSION_ID_KEY = "DEVICE_SESSION_ID";
    public static String FETCH_TRIP_STATUS_TIME_INTERVAL_KEY = "FETCH_TRIP_STATUS_TIME_INTERVAL";
    public static String RIDER_REQUEST_ACCEPT_TIME_KEY = "RIDER_REQUEST_ACCEPT_TIME";
    public static String SC_CONNECT_URL_KEY = "SC_CONNECT_URL";
    public static String GOOGLE_SERVER_ANDROID_PASSENGER_APP_KEY = "GOOGLE_SERVER_ANDROID_PASSENGER_APP_KEY";
    public static String dateFormateInHeaderBar = "EEE, MMM d, yyyy";
    public static String dateFormateInList = "dd-MMM-yyyy";
    public static String DefaultDatefromate = "yyyy-MM-dd";
    public static String OriginalDateFormate = "yyyy-MM-dd HH:mm:ss";
    public static String dateFormateForBooking = "dd MMM yyyy";
    public static String VERIFICATION_CODE_RESEND_TIME_IN_SECONDS_KEY = "VERIFICATION_CODE_RESEND_TIME_IN_SECONDS";
    public static String VERIFICATION_CODE_RESEND_COUNT_KEY = "VERIFICATION_CODE_RESEND_COUNT";
    public static String VERIFICATION_CODE_RESEND_COUNT_RESTRICTION_KEY = "VERIFICATION_CODE_RESEND_COUNT_RESTRICTION";
    public static String DefaultCountry = "vDefaultCountry";
    public static String DefaultCountryCode = "vDefaultCountryCode";
    public static String DefaultPhoneCode = "vDefaultPhoneCode";
    public static String app_type = "User";
    public static String languageLabelsKey = "LanguageLabel";
    public static String APP_GCM_SENDER_ID_KEY = "APP_GCM_SENDER_ID";
    public static String MOBILE_VERIFICATION_ENABLE_KEY = "MOBILE_VERIFICATION_ENABLE";
    public static String FACEBOOK_APPID_KEY = "FACEBOOK_APPID";
    public static String LINK_FORGET_PASS_KEY = "LINK_FORGET_PASS_PAGE_PASSENGER";
    public static String LANGUAGE_LIST_KEY = "LANGUAGELIST";
    public static String CURRENCY_LIST_KEY = "CURRENCYLIST";
    public static String LANGUAGE_IS_RTL_KEY = "LANG_IS_RTL";
    public static String GOOGLE_MAP_LANGUAGE_CODE_KEY = "GOOGLE_MAP_LANG_CODE";
    public static String REFERRAL_SCHEME_ENABLE = "REFERRAL_SCHEME_ENABLE";
    public static String SITE_TYPE_KEY = "SITE_TYPE";
    public static String WALLET_ENABLE = "WALLET_ENABLE";
    public static String DATABASE_RTL_STR = "rtl";
    public static String LANGUAGE_CODE_KEY = "LANG_CODE";
    public static String isUserLogIn = "IsUserLoggedIn";
    public static String iMemberId;
    public static String APP_TYPE;
    public static String action_str;
    public static String message_str;
    public static String message_str_one;
    public static String UBERX_PARENT_CAT_ID;
    public static String APP_DESTINATION_MODE;
    public static String ENABLE_TOLL_COST;
    public static String TOLL_COST_APP_ID;
    public static String TOLL_COST_APP_CODE;
    public static String FEMALE_RIDE_REQ_ENABLE;
    public static String PREF_FEMALE;
    public static String PREF_HANDICAP;
    public static String DEFAULT_LANGUAGE_VALUE;
    public static String DEFAULT_CURRENCY_VALUE;
    public static String PUBSUB_TECHNIQUE;
    public static String YALGAAR_CLIENT_KEY;
    public static String NONE_DESTINATION;
    public static String STRICT_DESTINATION;
    public static String NON_STRICT_DESTINATION;
    public static String GCM_FAILED_KEY;
    public static String APNS_FAILED_KEY;
    public static String SELECTEDRIVERID;
    public static String FACEBOOK_LOGIN;
    public static String GOOGLE_LOGIN;
    public static String TWITTER_LOGIN;
    public static String LINKDIN_LOGIN;
    public static String isCardAdded;
    public static String ISWALLETBALNCECHANGE;
    public static String PUBSUB_PUBLISH_DRIVER_LOC_DISTANCE_LIMIT;
    public static String dateFormateTimeOnly;
    public static String LINK_SIGN_UP_PAGE_KEY;
    public static String DRIVER_CURRENT_REQ_OPEN_KEY;
    public static String IsTripStarted;
    public static String DriverWaitingTime;
    public static String DriverWaitingSecTime;
    public static String GO_ONLINE_KEY;
    public static String LAST_FINISH_TRIP_TIME_KEY;
    public static String PHOTO_UPLOAD_SERVICE_ENABLE_KEY;
    public static String DRIVER_ONLINE_KEY;
    public static String DRIVER_REQ_CODE_PREFIX_KEY;
    public static String DRIVER_ACTIVE_REQ_MSG_KEY;
    public static String DRIVER_REQ_COMPLETED_MSG_CODE_KEY;
    public static String BACKGROUND_APP_RECEIVER_INTENT_ACTION;
    public static String passenger_message_arrived_intent_key;
    public static String WORKLOCATION;
    public static String GOOGLE_SERVER_ANDROID_DRIVER_APP_KEY;
    public static String MAX_ALLOW_NUM_DESTINATION_MULTI_KEY;
    public static String ENABLE_ROUTE_CALCULATION_MULTI_KEY;
    public static String ENABLE_ROUTE_OPTIMIZE_MULTI_KEY;
    public static String ALLOW_MULTIPLE_DEST_ADD_KEY;
    public static final String PUBNUB_DISABLED_KEY = "PUBNUB_DISABLED";
    public static final String ENABLE_SOCKET_CLUSTER_KEY = "ENABLE_SOCKET_CLUSTER";
    public static boolean SKIP_MOCK_LOCATION_CHECK;
    public static String serviceCategoriesKey;
    public static String GOOGLE_SERVER_ANDROID_STORE_APP_KEY;
    public static String eSystem_Type;
    public static String BASKET_ITEMS;
    public static String STORE_ID;
    public static String STORE_MINIMUM_ORDER;
    public static String STORE_MAX_QTY;
    public static String SELECT_ADDRESSS;
    public static String SELECT_LATITUDE;
    public static String SELECT_LONGITUDE;
    public static String SELECT_ADDRESS_ID;
    public static String CURRENT_ADDRESSS;
    public static String CURRENT_LATITUDE;
    public static String CURRENT_LONGITUDE;
    public static String iServiceId_KEY;
    public static String DEFAULT_STATE_VALUE;
    public static String DEFAULT_CITY_VALUE;
    public static String isMultiTrackRunning;
    public static String TrackDateFormatewithTime;
    public static String DELIVERALL_KEY;
    public static String ONLYDELIVERALL_KEY;
    public static String passenger_app_type;
    public static String data_str;
    public static String WalletApiFormate;
    public static String DateFormateInDetailScreen;
    public static String ERROR_EMPTY_TXT = "Required field";
    public static String ERROR_INAVLID_FORMAT_TXT = "Invalid format";
    public static String ERROR_INAVLID_MOBILE_FORMAT_TXT = "Invalid mobile format";
    public static String ERROR_INAVLID_EMAIL_FORMAT_TXT = "Invalid email";
    public static String EMAIL_PATTERN = "[a-zA-Z0-9._-]+@[a-z]+\\.+[a-z]+";
    public static String USER_FIRST_LOGIN;
    public static String APP_GENERAL_DATA;
    public static final int RC_PERMISSION = 10;
    public static final int RC_CLAIM_PRODUCT = 11;
    public static final int RC_USE_VOUCHER = 12;
    public static final int RC_FIND_ITEM = 13;
    public static final int RC_FIND_STORE = 14;

    public Utils() {
    }
    

    public static int dipToPixels(Context context, float dipValue) {
        DisplayMetrics var2 = context.getResources().getDisplayMetrics();
        @SuppressLint("WrongConstant") int i = (int) TypedValue.applyDimension(1, dipValue, var2);
        return i;
    }

    public static int getExifRotation(String path) {
        ExifInterface var1 = null;

        try {
            var1 = new ExifInterface(path);
        } catch (IOException var3) {
            var3.printStackTrace();
        }

        int var2 = var1.getAttributeInt("Orientation", 0);
        return var2;
    }

    public static void setMenuTextColor(MenuItem item, int color) {
        SpannableString var2 = new SpannableString(item.getTitle());
        var2.setSpan(new ForegroundColorSpan(color), 0, var2.length(), 0);
        item.setTitle(var2);
    }

    public static String removeWithSpace(String data) {
        return data.replaceAll("\\s+", " ");
    }

    public static int generateViewId() {
        if (Build.VERSION.SDK_INT >= 17) {
            return View.generateViewId();
        } else {
            int var0;
            int var1;
            do {
                var0 = a.get();
                var1 = var0 + 1;
                if (var1 > 16777215) {
                    var1 = 1;
                }
            } while(!a.compareAndSet(var0, var1));

            return var0;
        }
    }

    public static void runGC() {
    }

    public static void removeInput(EditText editBox) {
        editBox.setInputType(0);
        editBox.setFocusableInTouchMode(false);
        editBox.setFocusable(false);
        editBox.setOnTouchListener((var0, var1) -> {
            return true;
        });
    }

    public static boolean checkText(AppCompatEditText editBox) {
        return !getText(editBox).trim().equals("") && !TextUtils.isEmpty(editBox.getText());
    }

    public static boolean checkText(String text) {
        return text == null || !text.trim().equals("") && !TextUtils.isEmpty(text);
    }

    public static boolean checkText(EditText editBox) {
        return !getText(editBox).trim().equals("");
    }

    public static String getText(AppCompatEditText editBox) {
        return editBox.getText().toString().trim();
    }

    public static String getText(EditText editBox) {
        return editBox.getText().toString().trim();
    }

    public static String getText(AppCompatTextView textView) {
        return textView.getText().toString().trim();
    }

    public static boolean setErrorFields(AppCompatTextView editBox, String error) {
        editBox.setError(error);
        return false;
    }

    public static boolean setErrorFields(EditText editBox, String error) {
        editBox.setError(error);
        return false;
    }

    public static String maskCardNumber(String cardNumber) {
        int var1 = 0;

        StringBuffer var2;
        for(var2 = new StringBuffer(); var1 < cardNumber.length(); ++var1) {
            if (var1 > cardNumber.length() - 5) {
                var2.append(cardNumber.charAt(var1));
            } else {
                var2.append("X");
            }
        }

        return var2.toString();
    }

    public static Date convertStringToDate(String format, String date) {
        SimpleDateFormat var2 = new SimpleDateFormat(format);

        try {
            Date var3 = var2.parse(date);
            return var3;
        } catch (Exception var4) {
            var4.printStackTrace();
            return null;
        }
    }

    public static String convertDateToFormat(String format, Date date) {
        SimpleDateFormat var2 = new SimpleDateFormat(format, Locale.US);
        return var2.format(date);
    }

    public static boolean isValidTimeSelect(Date selectedDate, long timeOffset) {
        return selectedDate.getTime() >= System.currentTimeMillis() + timeOffset;
    }

    public static boolean isValidTimeSelectForLater(Date selectedDate, long timeOffset) {
        return selectedDate.getTime() <= System.currentTimeMillis() + timeOffset;
    }

    public static void hideKeyboard(Context context) {
        if (context != null && context instanceof Activity) {
            hideKeyboard((Activity)context);
        }

    }

    @SuppressLint("WrongConstant")
    public static void hideKeyboard(Activity act) {
        if (act != null && act instanceof Activity) {
            act.getWindow().setSoftInputMode(3);
            act.getWindow().setSoftInputMode(3);
            View var1 = act.getCurrentFocus();
            if (var1 != null) {
                InputMethodManager var2;
                var2 = (InputMethodManager)act.getSystemService("input_method");
                var2.hideSoftInputFromWindow(var1.getWindowToken(), 0);
            }
        }

    }

    public static void hideKeyPad(Activity act) {
        @SuppressLint("WrongConstant") InputMethodManager var1 = (InputMethodManager)act.getSystemService("input_method");
        var1.toggleSoftInput(1, 0);
    }

//    public static void setAppLocal(Context mContext) {
//        GeneralFunctions var1 = new GeneralFunctions(mContext);
//        String var2 = var1.retrieveValue(GOOGLE_MAP_LANGUAGE_CODE_KEY);
//        String var3 = var2.trim().equals("") ? "en" : var2;
//        Locale var4 = new Locale(var3, mContext.getResources().getConfiguration().locale.getCountry());
//        Locale.setDefault(var4);
//        if (Build.VERSION.SDK_INT >= 24) {
//            a(mContext, var4);
//        } else {
//            b(mContext, var4);
//        }
//    }

    @TargetApi(24)
    private static Context a(Context var0, Locale var1) {
        Configuration var2 = var0.getResources().getConfiguration();
        var2.setLocale(var1);
        return var0.createConfigurationContext(var2);
    }

    private static Context b(Context var0, Locale var1) {
        Resources var2 = var0.getResources();
        Configuration var3 = var2.getConfiguration();
        var3.locale = var1;
        var2.updateConfiguration(var3, var2.getDisplayMetrics());
        return var0;
    }

    public static void clearApplicationData(Context mContext) {
        File var1 = mContext.getCacheDir();
        File var2 = new File(var1.getParent());
        if (var2.exists()) {
            String[] var3 = var2.list();
            String[] var4 = var3;
            int var5 = var3.length;

            for(int var6 = 0; var6 < var5; ++var6) {
                String var7 = var4[var6];
                if (!var7.equals("lib")) {
                    deleteDir(new File(var2, var7));
                }
            }
        }

    }

    public static boolean deleteDir(File dir) {
        if (dir != null && dir.isDirectory()) {
            String[] var1 = dir.list();

            for(int var2 = 0; var2 < var1.length; ++var2) {
                boolean var3 = deleteDir(new File(dir, var1[var2]));
                if (!var3) {
                    return false;
                }
            }
        }

        return dir.delete();
    }

    public static double CalculationByLocation(double lat1, double lon1, double lat2, double lon2, String returnType) {
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
        int var34 = Integer.valueOf(var30.format(var32));
        return returnType.equals("METER") ? (double)var34 : (double)var9 * var24;
    }

    public static int dpToPx(Context context, float dpValue) {
        DisplayMetrics var2 = context.getResources().getDisplayMetrics();
        int var3 = Math.round(dpValue * (var2.xdpi / 160.0F));
        return var3;
    }

    public static int pxToDp(Context context, float pxValue) {
        DisplayMetrics var2 = context.getResources().getDisplayMetrics();
        int var3 = Math.round(pxValue / (var2.xdpi / 160.0F));
        return var3;
    }

    @SuppressLint("WrongConstant")
    public static Intent getLauncherIntent(Context mContext) {
        Intent var1 = mContext.getPackageManager().getLaunchIntentForPackage(mContext.getPackageName());
        var1.setFlags(67108864);
        return var1;
    }

    @SuppressLint("WrongConstant")
    public static Intent getPreviousIntent(Context context) {
        Intent var1 = null;
        ActivityManager var2 = (ActivityManager)context.getSystemService("activity");
        List var3 = var2.getRecentTasks(1024, 0);
        String var4 = context.getPackageName();
        if (!var3.isEmpty()) {
            for(int var6 = 0; var6 < var3.size(); ++var6) {
                ActivityManager.RecentTaskInfo var5 = (ActivityManager.RecentTaskInfo)var3.get(var6);
                if (var5.baseIntent.getComponent().getPackageName().equals(var4)) {
                    var1 = var5.baseIntent;
                    var1.setFlags(268_435_456);
                }
            }
        }

        return var1;
    }

    public static String getFileExt(String fileName) {
        return fileName.substring(fileName.lastIndexOf(".") + 1, fileName.length());
    }

    public static String getUserDeviceCountryCode(Context context) {
        if (context == null) {
            return "";
        } else {
            try {
                @SuppressLint("WrongConstant") TelephonyManager var1 = (TelephonyManager) context.getSystemService("phone");
                String var2 = var1.getSimCountryIso();
                if (var2 != null && var2.length() == 2) {
                    return var2.toLowerCase(Locale.US);
                }

                if (var1.getPhoneType() != 2) {
                    String var3 = var1.getNetworkCountryIso();
                    if (var3 != null && var3.length() == 2) {
                        return var3.toLowerCase(Locale.US);
                    }
                }
            } catch (Exception var5) {
                System.out.println("TelephonyError: Details: " + var5.getMessage());
            }

            String var6 = "";

            try {
                if (Build.VERSION.SDK_INT >= 24) {
                    var6 = context.getResources().getConfiguration().getLocales().get(0).getCountry();
                } else {
                    var6 = context.getResources().getConfiguration().locale.getCountry();
                }
            } catch (Exception var4) {
                System.out.println("LocalizedCountryCodeError: Details:" + var4.getMessage());
            }

            return var6;
        }
    }

    public static void setBlurImage(Bitmap bitmap_profile_icon, ImageView profileimageback) {
        Bitmap var2 = fastblur(bitmap_profile_icon, 95);
        profileimageback.setImageBitmap(var2);
        profileimageback.invalidate();
    }

    public static Bitmap fastblur(Bitmap sentBitmap, int radius) {
        Bitmap var2 = sentBitmap.copy(sentBitmap.getConfig(), true);
        if (radius < 1) {
            return null;
        } else {
            int var3 = var2.getWidth();
            int var4 = var2.getHeight();
            int[] var5 = new int[var3 * var4];
            var2.getPixels(var5, 0, var3, 0, 0, var3, var4);
            int var6 = var3 - 1;
            int var7 = var4 - 1;
            int var8 = var3 * var4;
            int var9 = radius + radius + 1;
            int[] var10 = new int[var8];
            int[] var11 = new int[var8];
            int[] var12 = new int[var8];
            int[] var23 = new int[Math.max(var3, var4)];
            int var24 = var9 + 1 >> 1;
            var24 *= var24;
            int[] var25 = new int[256 * var24];

            int var18;
            for(var18 = 0; var18 < 256 * var24; ++var18) {
                var25[var18] = var18 / var24;
            }

            int var21 = 0;
            int var22 = 0;
            int[][] var26 = new int[var9][3];
            int var31 = radius + 1;

            int var13;
            int var14;
            int var15;
            int var16;
            int var17;
            int var19;
            int var27;
            int var28;
            int[] var29;
            int var30;
            int var32;
            int var33;
            int var34;
            int var35;
            int var36;
            int var37;
            for(var17 = 0; var17 < var4; ++var17) {
                var15 = 0;
                var14 = 0;
                var13 = 0;
                var34 = 0;
                var33 = 0;
                var32 = 0;
                var37 = 0;
                var36 = 0;
                var35 = 0;

                for(var18 = -radius; var18 <= radius; ++var18) {
                    var19 = var5[var21 + Math.min(var6, Math.max(var18, 0))];
                    var29 = var26[var18 + radius];
                    var29[0] = (var19 & 16711680) >> 16;
                    var29[1] = (var19 & '\uff00') >> 8;
                    var29[2] = var19 & 255;
                    var30 = var31 - Math.abs(var18);
                    var13 += var29[0] * var30;
                    var14 += var29[1] * var30;
                    var15 += var29[2] * var30;
                    if (var18 > 0) {
                        var35 += var29[0];
                        var36 += var29[1];
                        var37 += var29[2];
                    } else {
                        var32 += var29[0];
                        var33 += var29[1];
                        var34 += var29[2];
                    }
                }

                var27 = radius;

                for(var16 = 0; var16 < var3; ++var16) {
                    var10[var21] = var25[var13];
                    var11[var21] = var25[var14];
                    var12[var21] = var25[var15];
                    var13 -= var32;
                    var14 -= var33;
                    var15 -= var34;
                    var28 = var27 - radius + var9;
                    var29 = var26[var28 % var9];
                    var32 -= var29[0];
                    var33 -= var29[1];
                    var34 -= var29[2];
                    if (var17 == 0) {
                        var23[var16] = Math.min(var16 + radius + 1, var6);
                    }

                    var19 = var5[var22 + var23[var16]];
                    var29[0] = (var19 & 16711680) >> 16;
                    var29[1] = (var19 & '\uff00') >> 8;
                    var29[2] = var19 & 255;
                    var35 += var29[0];
                    var36 += var29[1];
                    var37 += var29[2];
                    var13 += var35;
                    var14 += var36;
                    var15 += var37;
                    var27 = (var27 + 1) % var9;
                    var29 = var26[var27 % var9];
                    var32 += var29[0];
                    var33 += var29[1];
                    var34 += var29[2];
                    var35 -= var29[0];
                    var36 -= var29[1];
                    var37 -= var29[2];
                    ++var21;
                }

                var22 += var3;
            }

            for(var16 = 0; var16 < var3; ++var16) {
                var15 = 0;
                var14 = 0;
                var13 = 0;
                var34 = 0;
                var33 = 0;
                var32 = 0;
                var37 = 0;
                var36 = 0;
                var35 = 0;
                int var20 = -radius * var3;

                for(var18 = -radius; var18 <= radius; ++var18) {
                    var21 = Math.max(0, var20) + var16;
                    var29 = var26[var18 + radius];
                    var29[0] = var10[var21];
                    var29[1] = var11[var21];
                    var29[2] = var12[var21];
                    var30 = var31 - Math.abs(var18);
                    var13 += var10[var21] * var30;
                    var14 += var11[var21] * var30;
                    var15 += var12[var21] * var30;
                    if (var18 > 0) {
                        var35 += var29[0];
                        var36 += var29[1];
                        var37 += var29[2];
                    } else {
                        var32 += var29[0];
                        var33 += var29[1];
                        var34 += var29[2];
                    }

                    if (var18 < var7) {
                        var20 += var3;
                    }
                }

                var21 = var16;
                var27 = radius;

                for(var17 = 0; var17 < var4; ++var17) {
                    var5[var21] = -16777216 & var5[var21] | var25[var13] << 16 | var25[var14] << 8 | var25[var15];
                    var13 -= var32;
                    var14 -= var33;
                    var15 -= var34;
                    var28 = var27 - radius + var9;
                    var29 = var26[var28 % var9];
                    var32 -= var29[0];
                    var33 -= var29[1];
                    var34 -= var29[2];
                    if (var16 == 0) {
                        var23[var17] = Math.min(var17 + var31, var7) * var3;
                    }

                    var19 = var16 + var23[var17];
                    var29[0] = var10[var19];
                    var29[1] = var11[var19];
                    var29[2] = var12[var19];
                    var35 += var29[0];
                    var36 += var29[1];
                    var37 += var29[2];
                    var13 += var35;
                    var14 += var36;
                    var15 += var37;
                    var27 = (var27 + 1) % var9;
                    var29 = var26[var27];
                    var32 += var29[0];
                    var33 += var29[1];
                    var34 += var29[2];
                    var35 -= var29[0];
                    var36 -= var29[1];
                    var37 -= var29[2];
                    var21 += var3;
                }
            }

            var2.setPixels(var5, 0, var3, 0, 0, var3, var4);
            return var2;
        }
    }

    public static float getScreenPixelWidth(Context mContext) {
        DisplayMetrics var1 = mContext.getResources().getDisplayMetrics();
        float var2 = (float)var1.widthPixels;
        return var2;
    }

    public static float getScreenPixelHeight(Context mContext) {
        DisplayMetrics var1 = mContext.getResources().getDisplayMetrics();
        float var2 = (float)var1.heightPixels;
        return var2;
    }

    public static int getWidthOfBanner(Context mContext, int widthOffsetDpValueInPixel) {
        return (int)(getScreenPixelWidth(mContext) - (float)widthOffsetDpValueInPixel);
    }

    public static int getHeightOfBanner(Context mContext, int widthOffsetDpValueInPixel, String ratio) {
        return ratio.equalsIgnoreCase("4:3") ? (int)((double)getWidthOfBanner(mContext, widthOffsetDpValueInPixel) / 1.33333333333D) : (int)((double)getWidthOfBanner(mContext, widthOffsetDpValueInPixel) / 1.77777778D);
    }

//    public static String getResizeImgURL(Context mContext, String imgUrl, int width, int height) {
//        String var4 = GeneralFunctions.retrieveValue("SERVERURL", mContext);
//        imgUrl = imgUrl.replace(" ", "%20");
//        String var5 = var4.endsWith("/") ? var4 : var4 + "/";
//        var5 = var5 + "resizeImg.php?src=" + imgUrl + "&w=" + width + "&h=" + height;
//        return var5;
//    }

//    public static String getResizeImgURL(Context mContext, String imgUrl, int width, int height, int MAX_WIDTH) {
//        String var5 = GeneralFunctions.retrieveValue("SERVERURL", mContext);
//        imgUrl = imgUrl.replace(" ", "%20");
//        String var6 = var5.endsWith("/") ? var5 : var5 + "/";
//        var6 = var6 + "resizeImg.php?src=" + imgUrl + "&w=" + width + "&h=" + height + "&IMG_MAX_WIDTH=" + MAX_WIDTH;
//        return var6;
//    }
//
//    public static String getResizeImgURL(Context mContext, String imgUrl, int width, int height, float MAX_HEIGHT) {
//        String var5 = GeneralFunctions.retrieveValue("SERVERURL", mContext);
//        imgUrl = imgUrl.replace(" ", "%20");
//        String var6 = var5.endsWith("/") ? var5 : var5 + "/";
//        var6 = var6 + "resizeImg.php?src=" + imgUrl + "&w=" + width + "&h=" + height + "&IMG_MAX_HEIGHT=" + MAX_HEIGHT;
//        return var6;
//    }
//
//    public static String getResizeImgURL(Context mContext, String imgUrl, int width, int height, int MAX_WIDTH, int MAX_HEIGHT) {
//        String var6 = GeneralFunctions.retrieveValue("SERVERURL", mContext);
//        imgUrl = imgUrl.replace(" ", "%20");
//        String var7 = var6.endsWith("/") ? var6 : var6 + "/";
//        var7 = var7 + "resizeImg.php?src=" + imgUrl + "&w=" + width + "&h=" + height + "&IMG_MAX_WIDTH=" + MAX_WIDTH + "&IMG_MAX_HEIGHT=" + MAX_HEIGHT;
//        return var7;
//    }

    public static boolean isAppInstalled(Context context, String packageName) {
        try {
            context.getPackageManager().getApplicationInfo(packageName, 0);
            return true;
        } catch (PackageManager.NameNotFoundException var3) {
            return false;
        }
    }

    public static void showSoftKeyboard(Context mContext, EditText view) {
        if (mContext instanceof Activity) {
            if (view.requestFocus()) {
                ((Activity)mContext).getWindow().setSoftInputMode(4);
                @SuppressLint("WrongConstant") InputMethodManager var2 = (InputMethodManager)mContext.getSystemService("input_method");
                var2.toggleSoftInput(2, 1);
            }

        }
    }

    public static void hideSoftKeyboard(Context mContext, View view) {
        if (mContext instanceof Activity) {
            ((Activity)mContext).getWindow().setSoftInputMode(2);
            @SuppressLint("WrongConstant") InputMethodManager var2 = (InputMethodManager)((Activity)mContext).getSystemService("input_method");
            var2.hideSoftInputFromWindow(view.getWindowToken(), 0);
        }
    }

    public static boolean textIsRTLWords(String text) {
        try {
            char[] var1 = text.toCharArray();
            int var2 = var1.length;

            for(int var3 = 0; var3 < var2; ++var3) {
                char var4 = var1[var3];
                if (Character.UnicodeBlock.of(var4) == Character.UnicodeBlock.ARABIC || Build.VERSION.SDK_INT >= 19 && Character.UnicodeBlock.of(var4) == Character.UnicodeBlock.OLD_PERSIAN || Character.UnicodeBlock.of(var4) == Character.UnicodeBlock.HEBREW) {
                    return true;
                }
            }

            for(int var6 = 0; var6 < Character.codePointCount(text, 0, text.length()); ++var6) {
                var2 = text.codePointAt(var6);
                if (var2 >= 1536 && var2 <= 1791 || var2 == 64394 || var2 == 1662 || var2 == 1670 || var2 == 1711) {
                    return true;
                }
            }

            return false;
        } catch (Exception var5) {
            return false;
        }
    }

    public static void setBottomLineColor(Context mContext, View view, int colorOfBottomLine) {
        if (view != null) {
            Drawable var3 = view.getBackground();
            var3 = DrawableCompat.wrap(var3);
            DrawableCompat.setTint(var3, mContext.getResources().getColor(colorOfBottomLine));
            view.setBackground(var3);
        }
    }

    public static int dpToPx(float dp, Context context) {
        return dpToPx(dp, context.getResources());
    }

    public static int dpToPx(float dp, Resources resources) {
        @SuppressLint("WrongConstant") float var2 = TypedValue.applyDimension(1, dp, resources.getDisplayMetrics());
        return (int)var2;
    }

    public static Bitmap getBitmapFromView(View view) {
        view.measure(0, 0);
        view.layout(0, 0, view.getMeasuredWidth(), view.getMeasuredHeight());
        view.buildDrawingCache();
        Bitmap var1 = Bitmap.createBitmap(view.getMeasuredWidth(), view.getMeasuredHeight(), Bitmap.Config.ARGB_8888);
        Canvas var2 = new Canvas(var1);
        Drawable var3 = view.getBackground();
        if (var3 != null) {
            var3.draw(var2);
        } else {
            var2.drawColor(-1);
        }

        view.draw(var2);
        return var1;
    }

    public static int getSDKInt() {
        return Build.VERSION.SDK_INT;
    }

    public static boolean isValidImageResolution(String path) {
        BitmapFactory.Options var1 = new BitmapFactory.Options();
        var1.inJustDecodeBounds = true;
        BitmapFactory.decodeFile(path, var1);
        int var2 = var1.outWidth;
        int var3 = var1.outHeight;
        return var2 >= 256 && var3 >= 256;
    }

    public static void sendBroadCast(Context mContext, String action) {
        Intent var2 = new Intent(action);
        mContext.sendBroadcast(var2);
    }

    public static boolean isMyServiceRunning(Class<?> serviceClass, Context context) {
        @SuppressLint("WrongConstant") ActivityManager var2 = (ActivityManager)context.getSystemService("activity");
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

    public static String[] generateImageParams(String key, String content) {
        String[] var2 = new String[]{key, content};
        return var2;
    }

    public static String formatDate(String inputFormat, String outputFormat, String inputDate) {
        Date var3 = null;
        String var4 = "";
        SimpleDateFormat var5 = new SimpleDateFormat(inputFormat, Locale.getDefault());
        SimpleDateFormat var6 = new SimpleDateFormat(outputFormat, Locale.getDefault());

        try {
            var3 = var5.parse(inputDate);
            var4 = var6.format(var3);
        } catch (Exception var8) {
        }

        return var4;
    }

    public static void vibratePhone(Context mContext, long milliSeconds) {
        try {
            @SuppressLint("WrongConstant") Vibrator var3 = (Vibrator)mContext.getSystemService("vibrator");
            if (Build.VERSION.SDK_INT >= 26) {
                var3.vibrate(VibrationEffect.createOneShot(milliSeconds, -1));
            } else {
                var3.vibrate(milliSeconds);
            }
        } catch (Exception var4) {
        }

    }

    public static void playNotificationSound(Context mContext) {
        try {
            Uri var1 = RingtoneManager.getDefaultUri(2);
            Ringtone var2 = RingtoneManager.getRingtone(mContext, var1);
            var2.play();
        } catch (Exception var3) {
            var3.printStackTrace();
        }

    }

    public static void dismissBackGroundNotification(Context context) {
        @SuppressLint("WrongConstant") NotificationManager var1 = (NotificationManager)context.getSystemService("notification");
        var1.cancel(12);
        var1.cancelAll();
    }

    public static boolean isClassExist(String className) {
        try {
            Class.forName(className);
            return true;
        } catch (Exception var2) {
            return false;
        }
    }

    public static boolean isResourceFileExist(Context mContext, String resourceName, String resourcePath) {
        try {
            int var3 = mContext.getResources().getIdentifier(resourceName, resourcePath, mContext.getPackageName());
            if (var3 != 0) {
                return true;
            }
        } catch (Exception var4) {
        }

        return false;
    }

    static {
        iMemberId = USER_ID_KEY;
        APP_TYPE = "APP_TYPE";
        action_str = "action";
        message_str = "message";
        message_str_one = "message1";
        UBERX_PARENT_CAT_ID = "UBERX_PARENT_CAT_ID";
        APP_DESTINATION_MODE = "APP_DESTINATION_MODE";
        ENABLE_TOLL_COST = "ENABLE_TOLL_COST";
        TOLL_COST_APP_ID = "TOLL_COST_APP_ID";
        TOLL_COST_APP_CODE = "TOLL_COST_APP_CODE";
        FEMALE_RIDE_REQ_ENABLE = "FEMALE_RIDE_REQ_ENABLE";
        PREF_FEMALE = "Female_request";
        PREF_HANDICAP = "Handicap_request";
        DEFAULT_LANGUAGE_VALUE = "DEFAULT_LANGUAGE_VALUE";
        DEFAULT_CURRENCY_VALUE = "DEFAULT_CURRENCY_VALUE";
        PUBSUB_TECHNIQUE = "PUBSUB_TECHNIQUE";
        YALGAAR_CLIENT_KEY = "YALGAAR_CLIENT_KEY";
        NONE_DESTINATION = "None";
        STRICT_DESTINATION = "Strict";
        NON_STRICT_DESTINATION = "NonStrict";
        GCM_FAILED_KEY = "GCM_FAILED";
        APNS_FAILED_KEY = "APNS_FAILED";
        SELECTEDRIVERID = "SELECTEDRIVERID";
        FACEBOOK_LOGIN = "FACEBOOK_LOGIN";
        GOOGLE_LOGIN = "GOOGLE_LOGIN";
        TWITTER_LOGIN = "TWITTER_LOGIN";
        LINKDIN_LOGIN = "LINKDIN_LOGIN";
        isCardAdded = "isCardAdded";
        ISWALLETBALNCECHANGE = "ISWALLETBALNCECHANGE";
        PUBSUB_PUBLISH_DRIVER_LOC_DISTANCE_LIMIT = "PUBSUB_PUBLISH_DRIVER_LOC_DISTANCE_LIMIT";
        dateFormateTimeOnly = "h:mm a";
        LINK_SIGN_UP_PAGE_KEY = "LINK_SIGN_UP_PAGE_DRIVER";
        DRIVER_CURRENT_REQ_OPEN_KEY = "DRIVER_REQ_OPEN";
        IsTripStarted = "TripStart";
        DriverWaitingTime = "DriverWaitingTime";
        DriverWaitingSecTime = "DriverWaitingSecTime";
        GO_ONLINE_KEY = "GO_ONLINE";
        LAST_FINISH_TRIP_TIME_KEY = "LAST_FINISH_TRIP_TIME";
        PHOTO_UPLOAD_SERVICE_ENABLE_KEY = "PHOTO_UPLOAD_SERVICE_ENABLE";
        DRIVER_ONLINE_KEY = "IS_DRIVER_ONLINE";
        DRIVER_REQ_CODE_PREFIX_KEY = "REQUEST_CODE_";
        DRIVER_ACTIVE_REQ_MSG_KEY = "ACTIVE_REQUEST_MSG_";
        DRIVER_REQ_COMPLETED_MSG_CODE_KEY = "REQUEST_CODE_CONFIRMED_";
        BACKGROUND_APP_RECEIVER_INTENT_ACTION = "BACKGROUND_CALLBACK_ACTION";
        passenger_message_arrived_intent_key = "message";
        WORKLOCATION = "vWorkLocation";
        GOOGLE_SERVER_ANDROID_DRIVER_APP_KEY = "GOOGLE_SERVER_ANDROID_DRIVER_APP_KEY";
        MAX_ALLOW_NUM_DESTINATION_MULTI_KEY = "MAX_ALLOW_NUM_DESTINATION_MULTI";
        ENABLE_ROUTE_CALCULATION_MULTI_KEY = "ENABLE_ROUTE_CALCULATION_MULTI";
        ENABLE_ROUTE_OPTIMIZE_MULTI_KEY = "ENABLE_ROUTE_OPTIMIZE_MULTI";
        ALLOW_MULTIPLE_DEST_ADD_KEY = "ALLOW_MULTIPLE_DEST_ADD";
        SKIP_MOCK_LOCATION_CHECK = false;
        serviceCategoriesKey = "ServiceCategories";
        GOOGLE_SERVER_ANDROID_STORE_APP_KEY = "GOOGLE_SERVER_ANDROID_STORE_APP_KEY";
        eSystem_Type = "DeliverAll";
        BASKET_ITEMS = "BASKET_ITEMS";
        STORE_ID = "STORE_ID";
        STORE_MINIMUM_ORDER = "MINIMUM_ORDER";
        STORE_MAX_QTY = "MAX_QTY";
        SELECT_ADDRESSS = "SELECT_ADDRESSS";
        SELECT_LATITUDE = "SELECT_LATITUDE";
        SELECT_LONGITUDE = "SELECT_LONGITUDE";
        SELECT_ADDRESS_ID = "SELECT_ADDRESS_ID";
        CURRENT_ADDRESSS = "CURRENT_ADDRESSS";
        CURRENT_LATITUDE = "CURRENT_LATITUDE";
        CURRENT_LONGITUDE = "CURRENT_LONGITUDE";
        iServiceId_KEY = "iServiceId";
        DEFAULT_STATE_VALUE = "DEFAULT_STATE_VALUE";
        DEFAULT_CITY_VALUE = "DEFAULT_CITY_VALUE";
        isMultiTrackRunning = "No";
        TrackDateFormatewithTime = "dd MMM, hh:mm aa";
        DELIVERALL_KEY = "DELIVERALL";
        ONLYDELIVERALL_KEY = "ONLYDELIVERALL";
        passenger_app_type = "Passenger";
        data_str = "Data";
        WalletApiFormate = "dd-MMM-yyyy";
        DateFormateInDetailScreen = "EEE, MMM dd, yyyy hh:mm aaa";
        USER_FIRST_LOGIN = "USER_FIRST_LOGIN";
        APP_GENERAL_DATA = "APP_GENERAL_DATA";
    }
}
