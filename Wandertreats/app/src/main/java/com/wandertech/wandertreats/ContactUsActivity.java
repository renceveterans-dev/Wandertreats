package com.wandertech.wandertreats;

import android.app.Activity;
import android.content.Context;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.inputmethod.InputMethodManager;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.Toast;

import com.google.android.material.button.MaterialButton;
import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;
import com.wandertech.wandertreats.databinding.ActivityContactUsBinding;
import com.wandertech.wandertreats.databinding.ActivityTemplateBinding;
import com.wandertech.wandertreats.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.general.GeneralFunctions;
import com.wandertech.wandertreats.general.StartActProcess;
import com.wandertech.wandertreats.utils.Utils;

import java.util.HashMap;

import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;

public class ContactUsActivity extends BaseActivity {

    private RelativeLayout loader;
    private GeneralFunctions appFunctions;
    private AppCompatTextView title;
    private AppCompatImageView backImgView;
    private ActivityContactUsBinding binding;
    private TextInputLayout emailTxtLayout,subjectTxtLayout, messageTxtLayout;
    private TextInputEditText  emailTxt, subjectTxt, messageTxt;
    private AppCompatButton submitBtn;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityContactUsBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        backImgView = binding.toolbar.backImgView;

        emailTxt = binding.emailTxt;
        subjectTxt = binding.subjectTxt;
        messageTxt = binding.messageTxt;

        submitBtn = binding.submitBtn;

        emailTxtLayout = binding.emailTxtLayout;
        subjectTxtLayout = binding.emailTxtLayout;
        messageTxtLayout = binding.messageTxtLayout;

        emailTxt.addTextChangedListener(new setOnTextChangeAct(emailTxt));
        subjectTxt.addTextChangedListener(new setOnTextChangeAct(subjectTxt));
        messageTxt.addTextChangedListener(new setOnTextChangeAct( messageTxt));

        submitBtn.setOnClickListener(new setOnClickAct());
        backImgView.setOnClickListener(new setOnClickAct());

    }


    private Context getActContext() {

        return ContactUsActivity.this;
    }


    public class setOnClickAct implements View.OnClickListener {

        @Override
        public void onClick(View view) {

            switch(view.getId()){

                case R.id.submitBtn:
                    hideKeyboard((Activity) getActContext());

                    emailTxtLayout.setHelperText(null);
                    emailTxtLayout.setHelperTextEnabled(false);
                    emailTxtLayout.setErrorEnabled(false);

                    subjectTxtLayout.setHelperText(null);
                    subjectTxtLayout.setHelperTextEnabled(false);
                    subjectTxtLayout.setErrorEnabled(false);

                    messageTxtLayout.setHelperText(null);
                    subjectTxtLayout.setHelperTextEnabled(false);
                    subjectTxtLayout.setErrorEnabled(false);

                    if(emailTxt.getText().length() == 0 ){

                        emailTxtLayout.setErrorEnabled(true);
                        emailTxtLayout.setHelperTextEnabled(true);
                        emailTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    if(subjectTxt.getText().length() == 0 ){

                        subjectTxtLayout.setErrorEnabled(true);
                        subjectTxtLayout.setHelperTextEnabled(true);
                        subjectTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    if(messageTxt.getText().length() == 0 ){

                        messageTxtLayout.setErrorEnabled(true);
                        messageTxtLayout.setHelperTextEnabled(true);
                        messageTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    try{
                        submitTicket();
                    }catch (Exception e){
                        Toast.makeText(getActContext(), ""+e.toString(), Toast.LENGTH_SHORT).show();
                    }
                    break;

                case R.id.backImgView:

                    onBackPressed();

                    break;

                default:
                    break;

            }

        }

    }

    public static void hideKeyboard(Activity activity) {
        InputMethodManager imm = (InputMethodManager) activity.getSystemService(Activity.INPUT_METHOD_SERVICE);
        //Find the currently focused view, so we can grab the correct window token from it.
        View view = activity.getCurrentFocus();
        //If no view currently has focus, create a new one, just so we can grab a window token from it
        if (view == null) {
            view = new View(activity);
        }
        imm.hideSoftInputFromWindow(view.getWindowToken(), 0);
    }

    public void submitTicket() {


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "SUBMIT_TICKET");
        parameters.put("email", emailTxt.getText().toString() == null ? "" : emailTxt.getText().toString());
        parameters.put("subject", subjectTxt.getText().toString() == null ? "" : subjectTxt.getText().toString() );
        parameters.put("message", messageTxt.getText().toString() == null ? "" : messageTxt.getText().toString() );
        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_submit_ticket.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                // Toast.makeText(getActContext(),responseString, Toast.LENGTH_SHORT).show();

                if(responseString != null){

                    if(appFunctions.checkDataAvail(Utils.action_str, responseString)){



                    }else{


                    }

                }

            }
        });
        exeWebServer.execute();
    }

    public class setOnTextChangeAct implements TextWatcher {
        public TextInputEditText editText;
        public setOnTextChangeAct(TextInputEditText editText){
            this.editText = editText;
        }
        @Override
        public void beforeTextChanged(CharSequence s, int start, int count, int after) {
        }
        @Override
        public void onTextChanged(CharSequence s, int start, int before, int count) {

            messageTxtLayout.setHelperText(null);
            messageTxtLayout.setHelperTextEnabled(false);
            messageTxtLayout.setErrorEnabled(false);

            emailTxtLayout.setHelperText(null);
            emailTxtLayout.setHelperTextEnabled(false);
            emailTxtLayout.setErrorEnabled(false);

            subjectTxtLayout.setHelperText(null);
            subjectTxtLayout.setHelperTextEnabled(false);
            subjectTxtLayout.setErrorEnabled(false);

        }

        @Override
        public void afterTextChanged(Editable s) {
            switch (editText.getId()) {

                case R.id.emailTxt:

                    if(emailTxt.getText().length() == 0 ){

                        emailTxtLayout.setErrorEnabled(true);
                        emailTxtLayout.setHelperTextEnabled(true);
                        emailTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    break;

                case R.id.subjectTxt:

                    if(subjectTxt.getText().length() == 0 ){

                        subjectTxtLayout.setErrorEnabled(true);
                        subjectTxtLayout.setHelperTextEnabled(true);
                        subjectTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                case R.id.messageTxt:

                    if(messageTxt.getText().length() == 0 ){

                        messageTxtLayout.setErrorEnabled(true);
                        messageTxtLayout.setHelperTextEnabled(true);
                        messageTxtLayout.setHelperText("This field is required.");

                        return;
                    }

                    break;

            }
        }
    }
}
