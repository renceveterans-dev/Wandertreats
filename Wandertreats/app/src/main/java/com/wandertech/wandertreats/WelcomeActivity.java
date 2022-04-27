package com.wandertech.wandertreats;

import android.content.Context;
import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.SystemClock;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowInsets;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatButton;
import androidx.viewpager.widget.PagerAdapter;
import androidx.viewpager.widget.ViewPager;

import com.wandertech.wandertreats.databinding.ActivityLauncherBinding;
import com.wandertech.wandertreats.databinding.ActivityWelcomeBinding;
import com.wandertech.wandertreats.general.StartActProcess;

public class WelcomeActivity  extends AppCompatActivity {

    private ActivityWelcomeBinding binding;
    private View contentView;
    private final Handler handler = new Handler();
    private AppCompatButton loginBtn, registerBtn, homeBtn, storeBtn, productBtn, claimBtn,locationPickerBtn , searchLocBtn;
    private int[] screens;
    private TextView[] bottomBars;
    private LinearLayout layoutBars, layoubarsArea;
    private OnboardingViewPagerAdapter onboardingViewPagerAdapter;
    private ViewPager onBoardViewPager;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.white, this.getTheme()));
        } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            getWindow().setStatusBarColor(getResources().getColor(R.color.white));
        }

        getWindow().getDecorView().setSystemUiVisibility(View.SYSTEM_UI_FLAG_LIGHT_STATUS_BAR);//  set status text dark

        binding = ActivityWelcomeBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        contentView = binding.contentView;
        layoubarsArea = binding.layoubarsArea;
        layoutBars = binding.layoutBars;
        loginBtn = binding.loginBtn;
        registerBtn = binding.registerBtn;
        homeBtn = binding.homeBtn;
        storeBtn = binding.storeBtn;
        productBtn = binding.productBtn;
        claimBtn = binding.claimBtn;
        locationPickerBtn = binding.locationPickerBtn;
        searchLocBtn = binding.searchLocBtn;
        onBoardViewPager = binding.onBoardViewPager;

        loginBtn.setOnClickListener(new setOnClickAct());
        registerBtn.setOnClickListener(new setOnClickAct());
        homeBtn.setOnClickListener(new setOnClickAct());
        storeBtn.setOnClickListener(new setOnClickAct());
        productBtn.setOnClickListener(new setOnClickAct());
        claimBtn.setOnClickListener(new setOnClickAct());
        searchLocBtn.setOnClickListener(new setOnClickAct());
        locationPickerBtn.setOnClickListener(new setOnClickAct());

        screens = new int[]{
                R.layout.on_board_screen1,
                R.layout.on_board_screen2,
                R.layout.on_board_screen3,
        };
        ColoredBars(0);


        onboardingViewPagerAdapter = new OnboardingViewPagerAdapter();
        onBoardViewPager.setAdapter(onboardingViewPagerAdapter);
        onBoardViewPager.addOnPageChangeListener(new ViewPager.OnPageChangeListener() {

            @Override
            public void onPageSelected(int position) {
                ColoredBars(position);
                layoubarsArea.setVisibility(View.VISIBLE);
                if (position == screens.length - 1) {

//                    Next.setText("start");
//                    getStartedBtn.setVisibility(View.VISIBLE);
//                    Skip.setVisibility(View.GONE);
//                    Next.setVisibility(View.GONE);
                } else {
//                    Next.setVisibility(View.VISIBLE);
//                    layoubarsArea.setVisibility(View.VISIBLE);
//                    getStartedBtn.setVisibility(View.GONE);
//                    Skip.setVisibility(View.VISIBLE);
                }
            }

            @Override
            public void onPageScrolled(int arg0, float arg1, int arg2) {

            }

            @Override
            public void onPageScrollStateChanged(int arg0) {

            }
        });
    }

    public class setOnClickAct implements View.OnClickListener {

        private static final long MIN_CLICK_INTERVAL=600;
        private long mLastClickTime;

        @Override
        public void onClick(View view) {

            long currentClickTime= SystemClock.uptimeMillis();
            long elapsedTime=currentClickTime-mLastClickTime;

            mLastClickTime=currentClickTime;

            if(elapsedTime<=MIN_CLICK_INTERVAL)
                return;

            switch(view.getId()){

                case R.id.loginBtn:

                    new StartActProcess(getActContext()).startAct(LoginActivity.class);

                    break;
                case R.id.registerBtn:

                    new StartActProcess(getActContext()).startAct(RegisterActivity.class);

                    break;
                case R.id.homeBtn:

                    new StartActProcess(getActContext()).startAct(MainActivity.class);

                    break;

                case R.id.storeBtn:
                    new StartActProcess(getActContext()).startAct(StoreActivity.class);

                    break;

                case R.id.productBtn:

                    new StartActProcess(getActContext()).startAct(ProductActivity.class);
                    break;

                case R.id.claimBtn:
                    Bundle bn = new Bundle();
                    bn.putInt("SCAN_MODE", 11);
                    new StartActProcess(getActContext()).startAct(ProductActivity.class);
                    break;

                case R.id.locationPickerBtn:
                    new StartActProcess(getActContext()).startAct(LocationPickerActivity.class);
                    break;

                case R.id.searchLocBtn:
                    new StartActProcess(getActContext()).startAct(SearchLocationActivity.class);
                    break;

            }

        }


    }

    public class OnboardingViewPagerAdapter extends PagerAdapter {
        private LayoutInflater inflater;

        public OnboardingViewPagerAdapter() {
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

    public Context getActContext() {
        return WelcomeActivity.this;
    }
}

