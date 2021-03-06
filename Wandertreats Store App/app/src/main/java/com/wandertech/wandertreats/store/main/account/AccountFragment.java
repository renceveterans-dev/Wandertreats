package com.wandertech.wandertreats.store.main.account;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.graphics.drawable.ColorDrawable;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.SystemClock;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatTextView;
import androidx.appcompat.widget.LinearLayoutCompat;
import androidx.cardview.widget.CardView;
import androidx.fragment.app.Fragment;
import androidx.lifecycle.ViewModelProvider;
import de.hdodenhof.circleimageview.CircleImageView;

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
import com.hbb20.GThumb;
import com.wandertech.wandertreats.store.AboutActivity;
import com.wandertech.wandertreats.store.ContactUsActivity;
import com.wandertech.wandertreats.store.LocationPickerActivity;
import com.wandertech.wandertreats.store.MainActivity;
import com.wandertech.wandertreats.store.MyApp;
import com.wandertech.wandertreats.store.ProfileActivity;
import com.wandertech.wandertreats.store.R;
import com.wandertech.wandertreats.store.ReferralActivity;
import com.wandertech.wandertreats.store.SaveAddressActivity;
import com.wandertech.wandertreats.store.databinding.FragmentAccountBinding;
import com.wandertech.wandertreats.store.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.store.general.GeneralFunctions;
import com.wandertech.wandertreats.store.general.PopUpDialog;
import com.wandertech.wandertreats.store.general.ProgressDialog;
import com.wandertech.wandertreats.store.general.StartActProcess;
import com.wandertech.wandertreats.store.main.explore.ExploreViewModel;
import com.wandertech.wandertreats.store.utils.Constants;
import com.wandertech.wandertreats.store.utils.Utils;

import java.util.HashMap;
import java.util.Locale;
import java.util.Map;

public class AccountFragment  extends Fragment {

    private ExploreViewModel notificationsViewModel;
    private FragmentAccountBinding binding;
    public AppCompatTextView titleTxt;
    public LinearLayoutCompat profileArea, logoutArea,saveAddressArea, aboutArea, supportArea, referralArea;
    public GeneralFunctions appFunctions;
    private CircleImageView profile_image;
    private AppCompatTextView userNameTxt, userNameLabel;
    private String profileData = "";
    private LinearLayoutCompat verifyEmailBtn;
    private ImageView closeVerifyEmailBtn;
    private CardView verifyEmailArea;
    private MainActivity mainActivity;
    private GThumb profilePhoto;


    public View onCreateView(@NonNull LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {

        notificationsViewModel = new ViewModelProvider(this).get(ExploreViewModel.class);
        appFunctions = MyApp.getInstance().getGeneralFun(container.getContext());
        binding = FragmentAccountBinding.inflate(inflater, container, false);
        View root = binding.getRoot();

        if(getActivity() != null && isAdded()) {

            try {

                ((MainActivity) getActivity()).  getWindow().getDecorView().setSystemUiVisibility( View.SYSTEM_UI_FLAG_LAYOUT_STABLE | View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN);
                appFunctions.setWindowFlag((Activity) getActivity(), WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS ,false);

                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
                    ((MainActivity) getActivity()).  getWindow().setStatusBarColor(getResources().getColor(R.color.transparent,  ((MainActivity) getActivity()).getTheme()));
                } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
                    ((MainActivity) getActivity()). getWindow().setStatusBarColor(getResources().getColor(R.color.transparent));
                }

            }catch (Exception e){
                appFunctions.showMessage(e.toString());
            }


            profileData = appFunctions.retrieveValue(Utils.USER_PROFILE_JSON);

            profileArea = binding.profileArea;
            aboutArea = binding.aboutArea;
            supportArea = binding.supportArea;
            verifyEmailArea = binding.verifyEmailArea;
            saveAddressArea =binding.saveAddressArea;
            logoutArea = binding.logoutArea;
            referralArea = binding.referralArea;
            profilePhoto = binding.profileGthumb;

            verifyEmailBtn = binding.verifyEmailBtn;
            closeVerifyEmailBtn = binding.closeVerifyEmailBtn;

            profile_image = binding.profileImage;
            userNameTxt = binding.userNameTxt;
            userNameLabel = binding.userNameLabel;

            if(appFunctions.isEmailVerified()){

                verifyEmailArea.setVisibility(View.GONE);
            }

            profileArea.setOnClickListener(new setOnClickAct());
            saveAddressArea.setOnClickListener(new setOnClickAct());
            referralArea.setOnClickListener(new setOnClickAct());
            aboutArea.setOnClickListener(new setOnClickAct());
            supportArea.setOnClickListener(new setOnClickAct());
            logoutArea.setOnClickListener(new setOnClickAct());
            verifyEmailBtn.setOnClickListener(new setOnClickAct());
            closeVerifyEmailBtn.setOnClickListener(new setOnClickAct());


            setLabel();
        }



        return root;
    }

    public void setLabel(){
        userNameTxt.setText(appFunctions.getFullName());
        userNameLabel.setText(appFunctions.getUserName());

        profilePhoto.loadThumbForName("", appFunctions.getFullName().charAt(0)+"");

    }

    public Context getActContext(){
        return   getActivity();
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

                case R.id.referralArea:

                    new StartActProcess(getContext()).startAct(ReferralActivity.class);

                    break;

                case R.id.aboutArea:

                    new StartActProcess(getContext()).startAct(AboutActivity.class);

                    break;

                case R.id.saveAddressArea:

                    new StartActProcess(getContext()).startAct(SaveAddressActivity.class);

                    break;

                case R.id.supportArea:
                    new StartActProcess(getContext()).startAct(ContactUsActivity.class);
                    break;

                case R.id.profileArea:
                    new StartActProcess(getContext()).startAct(ProfileActivity.class);
                    break;

                case R.id.logoutArea:

                    try{

                        AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
                        LayoutInflater inflater = (LayoutInflater) getActivity().getSystemService( Context.LAYOUT_INFLATER_SERVICE );
                        View dialog = inflater.inflate( R.layout.dialog_confirm, null );

                        AppCompatTextView title = dialog.findViewById(R.id.title);
                        AppCompatTextView message = dialog.findViewById( R.id.message );
                        MaterialButton positive_btn = dialog.findViewById( R.id.positive_btn);
                        MaterialButton negtive_btn = dialog.findViewById( R.id.negative_btn);
                        builder.setView(dialog);

                        title.setText("Logout");
                        message.setText("Are you sure you want to logout?");
                        positive_btn.setText("Yes");
                        negtive_btn.setText("No");

                        AlertDialog alert = builder.create();
                        alert.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));
                        alert.show();

                        positive_btn.setOnClickListener(new View.OnClickListener() {
                            @Override
                            public void onClick(View view) {

                                alert.dismiss();
                                appFunctions.logout();
                            }
                        });

                        negtive_btn.setOnClickListener(new View.OnClickListener() {
                            @Override
                            public void onClick(View view) {
                                alert.dismiss();
                            }
                        });
                    }catch (Exception e){
                        appFunctions.showMessage(e.toString());
                    }



                    break;

                case R.id.closeVerifyEmailBtn:
                    appFunctions.collapse(verifyEmailArea, 500, 0);
                    new Handler().postDelayed(new Runnable() {
                        @Override
                        public void run() {
                            verifyEmailArea.setVisibility(View.GONE);
                        }
                    }, 500);
                    break;
                case R.id. verifyEmailBtn:

                    sendEmailVerification();

                    break;

            }
        }

    }

    private void sendEmailVerification(){

        AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
        LayoutInflater inflater = (LayoutInflater) getActivity().getSystemService( Context.LAYOUT_INFLATER_SERVICE );
        View dialog = inflater.inflate(R.layout.dialog_progress, null );
        builder.setView(dialog);
        AlertDialog myDialog = builder.create();
        myDialog.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));
        myDialog.setCancelable(false);
        myDialog.show();

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "SEND_EMAIL_VERIFICATION");
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("token", "sdsdsdsdsds");
        parameters.put("name", appFunctions.getJsonValue("vName", profileData));
        parameters.put("fullname", appFunctions.getFullName());
        parameters.put("email", appFunctions.getJsonValue("vEmail", profileData));
        parameters.put("latitude", appFunctions.retrieveValue(Utils.CURRENT_LATITUDE));
        parameters.put("longitude", appFunctions.retrieveValue(Utils.CURRENT_LONGITUDE));
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_send_email_verification.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true, appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {
                myDialog.dismiss();
                if(responseString != null){

                 //   appFunctions.showMessage(responseString);

                    try{

                        AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
                        LayoutInflater inflater = (LayoutInflater) getActivity().getSystemService( Context.LAYOUT_INFLATER_SERVICE );
                        View dialog = inflater.inflate( R.layout.dialog_alert_2, null );

                        AppCompatTextView title = dialog.findViewById(R.id.title);
                        AppCompatTextView message = dialog.findViewById( R.id.message );
                        MaterialButton positive_btn = dialog.findViewById( R.id.positive_btn);
                        MaterialButton negtive_btn = dialog.findViewById( R.id.negative_btn);
                        ImageView dialog_image = dialog.findViewById(R.id.dialog_image);
                        dialog_image.setImageResource(R.drawable.email_confirm);
                        View seperator4 = dialog.findViewById(R.id.seperator4);
                        builder.setView(dialog);

                        title.setVisibility(View.GONE);
                        message.setText("We have sent you an verification email to "+appFunctions.getJsonValue("vEmail", profileData));
                        positive_btn.setText("Okay");
                        
                        AlertDialog alert = builder.create();
                        alert.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));
                        alert.show();

                        positive_btn.setOnClickListener(new View.OnClickListener() {
                            @Override
                            public void onClick(View view) {

                                alert.dismiss();
                                appFunctions.collapse( verifyEmailArea, 500, 0);
                                new Handler().postDelayed(new Runnable() {
                                    @Override
                                    public void run() {
                                        verifyEmailArea.setVisibility(View.GONE);
                                    }
                                }, 500);

                            }
                        });

                    }catch (Exception e){
                        appFunctions.showMessage(e.toString());
                    }
                }else{
                    Toast.makeText(getActContext(), "Error "+ responseString, Toast.LENGTH_SHORT).show();
                }

            }
        });
        exeWebServer.execute();

    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
        binding = null;
    }
}