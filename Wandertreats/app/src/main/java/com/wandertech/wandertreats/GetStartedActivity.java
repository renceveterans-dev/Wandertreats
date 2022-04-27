package com.wandertech.wandertreats;

import android.app.ActivityOptions;
import android.content.Context;
import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

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
import com.google.android.material.button.MaterialButton;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.utils.Utils;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.HashMap;
import java.util.Map;

import androidx.appcompat.widget.AppCompatTextView;
import androidx.viewpager.widget.PagerAdapter;
import androidx.viewpager.widget.ViewPager;

public class GetStartedActivity extends BaseActivity {

    private LinearLayout Layout_bars, layoubarsArea;
    private GeneralFunctions appFunctions;
    private TextView[] bottomBars;
    private Intent mainIntent, mIntent;
    private MaterialButton getStartedBtn;
    private JSONArray profile;
    private JSONObject profile_obj;
    private int[] screens;
    private ImageView Next;
    private AppCompatTextView Skip;
    private ViewPager vp;
    private String redirectTo = "";
    private Bundle bndlAnimation;
    private MyViewPagerAdapter myvpAdapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_get_started);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        redirectTo =  getIntent().getStringExtra("on_board_screen") == null ? "" : getIntent().getStringExtra("on_board_screen");
        vp = (ViewPager) findViewById(R.id.view_pager);
        Layout_bars = (LinearLayout) findViewById(R.id.layoutBars);
        layoubarsArea = (LinearLayout)  findViewById(R.id.layoubarsArea);
        Skip = (AppCompatTextView) findViewById(R.id.skip);
        Next = (ImageView) findViewById(R.id.next);
        getStartedBtn = (MaterialButton)  findViewById(R.id.getStartedBtn);
        getStartedBtn.setTransformationMethod(null);
        getStartedBtn.setText("Get Started");

        if(!appFunctions.isFirstTimeLaunch()){

            launchMain(redirectTo);

        }else{
            appFunctions.setFirstTimeLaunch(false);
            screens = new int[]{
                    R.layout.on_board_screen1,
                    R.layout.on_board_screen2,
                    R.layout.on_board_screen3,
            };
            ColoredBars(0);
        }

        Next.setOnClickListener(new setOnClickAct());
        getStartedBtn.setOnClickListener(new setOnClickAct());
        Skip.setOnClickListener(new setOnClickAct());

        myvpAdapter = new MyViewPagerAdapter();
        vp.setAdapter(myvpAdapter);
        vp.addOnPageChangeListener(viewPagerPageChangeListener);



        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.white, this.getTheme()));
        } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.white));
        }

        getWindow().getDecorView().setSystemUiVisibility(View.SYSTEM_UI_FLAG_LIGHT_STATUS_BAR);//  set status text dark


    }

    @Override
    public void onBackPressed() {

    }

    private Context getActContext() {
        return GetStartedActivity.this;
    }


    private void ColoredBars(int thisScreen) {
        int[] colorsInactive = getResources().getIntArray(R.array.array_dot_inactive);
        int[] colorsActive = getResources().getIntArray(R.array.array_dot_active);
        bottomBars = new TextView[screens.length];

        layoubarsArea.removeAllViews();

        for (int i = 0; i < bottomBars.length; i++) {
            bottomBars[i] = new TextView(this);
            bottomBars[i].setTextSize(39);
            bottomBars[i].setGravity(Gravity.CENTER);
            bottomBars[i].setGravity(Gravity.CENTER_VERTICAL);
            bottomBars[i].setGravity(Gravity.CENTER_VERTICAL);
            bottomBars[i].setBackgroundResource(R.drawable.slider_inactive);
            bottomBars[i].setTextColor(colorsInactive[thisScreen]);
            layoubarsArea.addView(bottomBars[i]);
        }

        if (bottomBars.length > 0)
            bottomBars[thisScreen].setGravity(Gravity.CENTER);
        bottomBars[thisScreen].setGravity(Gravity.CENTER_VERTICAL);
        bottomBars[thisScreen].setBackgroundResource(R.drawable.slider_active);
        bottomBars[thisScreen].setTextColor(colorsActive[thisScreen]);
    }

    private int getItem(int i) {
        return vp.getCurrentItem() + i;
    }

    private void launchMain(String redirectTo) {

        try {
            bndlAnimation = ActivityOptions.makeCustomAnimation(getActContext(), R.anim.slideinleft, R.anim.slideinright).toBundle();

            appFunctions.setFirstTimeLaunch(false);
            mainIntent = new Intent(getActContext(), MainActivity.class);
            mainIntent.putExtra("on_board_screen", "HOME");
            mainIntent.putExtra("Redirect", "GetStarted");
            mainIntent.putExtra("latitude", appFunctions.retrieveValue(Utils.CURRENT_LATITUDE));
            mainIntent.putExtra("longitude", appFunctions.retrieveValue(Utils.CURRENT_LATITUDE));
            startActivity(mainIntent , bndlAnimation);
            finish();


        }catch (Exception e){

        }


    }

    ViewPager.OnPageChangeListener viewPagerPageChangeListener = new ViewPager.OnPageChangeListener() {

        @Override
        public void onPageSelected(int position) {
            ColoredBars(position);
            if (position == screens.length - 1) {
//                Next.setText("start");
                getStartedBtn.setVisibility(View.VISIBLE);
                layoubarsArea.setVisibility(View.GONE);
                Skip.setVisibility(View.GONE);
                Next.setVisibility(View.GONE);
            } else {
                Next.setVisibility(View.VISIBLE);
                layoubarsArea.setVisibility(View.VISIBLE);
                getStartedBtn.setVisibility(View.GONE);
                Skip.setVisibility(View.VISIBLE);
            }
        }

        @Override
        public void onPageScrolled(int arg0, float arg1, int arg2) {

        }

        @Override
        public void onPageScrollStateChanged(int arg0) {

        }
    };

    public class MyViewPagerAdapter extends PagerAdapter {
        private LayoutInflater inflater;

        public MyViewPagerAdapter() {
        }

        @Override
        public Object instantiateItem(ViewGroup container, int position) {
            inflater = (LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            View view = inflater.inflate(screens[position], container, false);
            container.addView(view);
            return view;
        }

        @Override
        public int getCount() {
            return screens.length;
        }

        @Override
        public void destroyItem(ViewGroup container, int position, Object object) {
            View v = (View) object;
            container.removeView(v);
        }

        @Override
        public boolean isViewFromObject(View v, Object object) {
            return v == object;
        }
    }

    public class setOnClickAct implements View.OnClickListener {

        @Override
        public void onClick(View view) {

            switch(view.getId()){

                case R.id.getStartedBtn:
                    launchMain(redirectTo);
                    break;

                case R.id.next:
                    int i = getItem(+1);
                    if (i < screens.length) {
                        vp.setCurrentItem(i);
                    } else {
                        launchMain(redirectTo);
                    }
                    break;

                case R.id.skip:

                    launchMain(redirectTo);;
                    break;



            }

        }


    }
}
