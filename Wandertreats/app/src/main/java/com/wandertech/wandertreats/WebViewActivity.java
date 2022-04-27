package com.wandertech.wandertreats;

import android.content.Context;
import android.os.Build;
import android.os.Bundle;
import android.view.View;
import android.webkit.CookieManager;
import android.webkit.WebChromeClient;
import android.webkit.WebSettings;
import android.webkit.WebStorage;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.ImageView;
import android.widget.RelativeLayout;

import com.wandertech.wandertreats.databinding.ActivityTemplateBinding;
import com.wandertech.wandertreats.databinding.ActivityWebviewBinding;
import com.wandertech.wandertreats.general.GeneralFunctions;

import androidx.annotation.RequiresApi;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;

public class WebViewActivity extends BaseActivity {

    private RelativeLayout loader;
    private GeneralFunctions appFunctions;
    private AppCompatTextView title;
    private ImageView backArrow;
    private ActivityWebviewBinding binding;
    private WebView  webview;
    private String activity = "";
    private String url = "";
    private AppCompatImageView backImgView;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);


        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityWebviewBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        activity = getIntent().getStringExtra("activity") == null ? "" : getIntent().getStringExtra("activity");
        webview = binding.webview;
        backImgView = binding.toolbar.backImgView;


        if(activity.equalsIgnoreCase("Customers Terms And Conditions")){

            url = "http://trikaroo.com.ph/terms_tricycledriver.php";
            title.setText("Terms And Conditions");
            setView(url);

        }else if(activity.equalsIgnoreCase("Customers Terms And Conditions")){


            url = "http://trikaroo.com.ph/terms_tricycledriver.php";
            title.setText("Privacy Policy");
            setView(url);

        }else if(activity.equalsIgnoreCase("Privacy Policy")){


            url = "http://trikaroo.com.ph/terms_privacypolicy.php";
            title.setText("Privacy Policy");
            setView(url);


        }else if(activity.equalsIgnoreCase("Trikoins User Terms and Condition")){

            url = "http://trikaroo.com.ph/terms_tricycledriver.php";
            title.setText("Trikoins User Terms and Condition");
            setView(url);


        }else if(activity.equalsIgnoreCase("Tricycle Driver Partner Terms and Conditions")){

            url = "http://trikaroo.com.ph/terms_tricycledriver.php";
            title.setText("Terms and Conditions");
            setView(url);

        }else if(activity.equalsIgnoreCase("Pabili")){


            url = "http://trikaroo.com.ph/user_pabilifood.php";
            title.setText("Help Center");
            setView(url);

        }else if(activity.equalsIgnoreCase("Pasakay")){

            url = "http://trikaroo.com.ph/user_pasakay.php";
            title.setText("Help Center");
            setView(url);

        }else if(activity.equalsIgnoreCase("Customer Trikoins")){

            url = "http://trikaroo.com.ph/trikoins_customer.php";
            title.setText("Help Center");
            setView(url);

        }else if(activity.equalsIgnoreCase("Driver Trikoins")){

            url = "http://trikaroo.com.ph/terms_trikoins.php";
            title.setText("DRIVER TRIKOINS");
            setView(url);


        }else if(activity.equalsIgnoreCase("PayPal")){
            String transactionId = getIntent().getStringExtra("transactionId");
            String amount = getIntent().getStringExtra("amount");
            String productName = getIntent().getStringExtra("productName");
            url = "https://mallody.ph/resources/paypal2/index.php?transactionId="+transactionId+"&amount="+amount+"&product=Cash In";
//            url = "htthttps://mallody.com.ph/beta/paypal/index.php?transactionId="+transactionId+"&amount="+fNetTotalAmount+"&product="+productName;
            title.setText("CASH IN");
            setView(url);
        }

    }


    @RequiresApi(api = Build.VERSION_CODES.LOLLIPOP)
    private void setView(String url){
        // Clear all the Application Cache, Web SQL Database and the HTML5 Web Storage
        WebStorage.getInstance().deleteAllData();

        // Clear all the cookies
        CookieManager.getInstance().removeAllCookies(null);
        CookieManager.getInstance().flush();

        webview.clearCache(true);
        webview.clearFormData();
        webview.clearHistory();
        webview.clearSslPreferences();

//        webview.setWebViewClient(new MyBrowser());
//        webview.setWebViewClient(new WebViewClient());


        webview.setWebChromeClient(new WebChromeClient());
        webview.setWebViewClient(new WebViewClient());
        webview.clearCache(true);
        webview.clearHistory();
        webview.getSettings().setJavaScriptEnabled(true);
        webview.getSettings().getPluginState();
        webview.getSettings().setMixedContentMode(WebSettings.MIXED_CONTENT_COMPATIBILITY_MODE);
        webview.getSettings().setJavaScriptCanOpenWindowsAutomatically(true);
        webview.getSettings().setPluginState(WebSettings.PluginState.ON);
        webview.getSettings().setDomStorageEnabled(true);
        webview.setScrollBarStyle(View.SCROLLBARS_INSIDE_OVERLAY);
        webview.loadUrl(url);

    }


    private Context getActContext() {

        return WebViewActivity.this;
    }


    public class setOnClickAct implements View.OnClickListener {

        @Override
        public void onClick(View view) {

            switch(view.getId()){

                case R.id. backImgView:
                    onBackPressed();
                    break;

                default:
                    break;

            }

        }


    }
}
