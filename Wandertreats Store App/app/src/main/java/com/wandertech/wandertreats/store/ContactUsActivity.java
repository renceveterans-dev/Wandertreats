package com.wandertech.wandertreats.store;;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.graphics.drawable.ColorDrawable;
import android.os.Bundle;
import android.os.Handler;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Patterns;
import android.view.LayoutInflater;
import android.view.View;
import android.view.inputmethod.InputMethodManager;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.Toast;

import com.google.android.material.appbar.MaterialToolbar;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;
import com.wandertech.wandertreats.store.databinding.ActivityContactUsBinding;
import com.wandertech.wandertreats.store.databinding.ActivityTemplateBinding;
import com.wandertech.wandertreats.store.general.ExecuteWebServiceApi;
import com.wandertech.wandertreats.store.general.GeneralFunctions;
import com.wandertech.wandertreats.store.general.StartActProcess;
import com.wandertech.wandertreats.store.utils.Utils;

import java.util.HashMap;

import androidx.appcompat.widget.AppCompatButton;
import androidx.appcompat.widget.AppCompatImageView;
import androidx.appcompat.widget.AppCompatTextView;

public class ContactUsActivity extends BaseActivity {

    private RelativeLayout loader;
    private GeneralFunctions appFunctions;
    private AppCompatTextView title;
    private ActivityContactUsBinding binding;
    private TextInputLayout emailTxtLayout,subjectTxtLayout, messageTxtLayout;
    private TextInputEditText  emailTxt, subjectTxt, messageTxt;
    private AppCompatButton submitBtn;
    private MaterialToolbar toolbar;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        appFunctions = MyApp.getInstance().getGeneralFun(getActContext());
        binding = ActivityContactUsBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        toolbar = binding.mainToolbar.toolbar;
        emailTxt = binding.emailTxt;
        subjectTxt = binding.subjectTxt;
        messageTxt = binding.messageTxt;

        submitBtn = binding.submitBtn;

        emailTxtLayout = binding.emailTxtLayout;
        subjectTxtLayout = binding.subjectTxtLayout;
        messageTxtLayout = binding.messageTxtLayout;

        emailTxt.addTextChangedListener(new setOnTextChangeAct(emailTxt));
        subjectTxt.addTextChangedListener(new setOnTextChangeAct(subjectTxt));
        messageTxt.addTextChangedListener(new setOnTextChangeAct( messageTxt));

        submitBtn.setOnClickListener(new setOnClickAct());
        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                onBackPressed();
            }
        });

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

                    if(emailTxt.getText().length() > 0  && !Patterns.EMAIL_ADDRESS.matcher(emailTxt.getText().toString().trim()).matches() ){
                        if(!emailTxtLayout.isErrorEnabled()){
                            emailTxtLayout.setErrorEnabled(true);
                            emailTxtLayout.setHelperTextEnabled(true);
                            emailTxtLayout.setHelperText("Invalid email.");
                        }
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
        parameters.put("userId", appFunctions.getMemberId());
        parameters.put("token", "");
        parameters.put("email", emailTxt.getText().toString() == null ? "" : emailTxt.getText().toString());
        parameters.put("subject", subjectTxt.getText().toString() == null ? "" : subjectTxt.getText().toString() );
        parameters.put("message", messageTxt.getText().toString() == null ? "" : messageTxt.getText().toString() );
        parameters.put("fullname", appFunctions.getFullName());
        parameters.put("name", appFunctions.getFirstName());

        parameters.put("userType", Utils.app_type);

        ExecuteWebServiceApi exeWebServer = new ExecuteWebServiceApi(getActContext(), parameters, "api_submit_ticket.php", true);
        exeWebServer.setLoaderConfig(getActContext(), true,appFunctions);
        exeWebServer.setDataResponseListener(new ExecuteWebServiceApi.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                //appFunctions.showMessage(responseString);

                if(responseString != null){

                    AlertDialog.Builder builder = new AlertDialog.Builder(getActContext());
                    LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService( Context.LAYOUT_INFLATER_SERVICE );
                    View dialog = inflater.inflate( R.layout.dialog_alert_2, null );

                    AppCompatTextView title = dialog.findViewById(R.id.title);
                    AppCompatTextView message = dialog.findViewById( R.id.message );
                    MaterialButton positive_btn = dialog.findViewById( R.id.positive_btn);
                    MaterialButton negtive_btn = dialog.findViewById( R.id.negative_btn);
                    ImageView dialog_image = dialog.findViewById(R.id.dialog_image);
                    dialog_image.setImageResource(R.drawable.chat_support);
                    View seperator4 = dialog.findViewById(R.id.seperator4);
                    builder.setView(dialog);

                    title.setVisibility(View.GONE);
                    message.setText("You ticket has been submitted. Please monitor your email and chat support representative will respond to you short. ");
                    positive_btn.setText("Okay");

                    AlertDialog alert = builder.create();
                    alert.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));
                    alert.show();

                    positive_btn.setOnClickListener(new View.OnClickListener() {
                        @Override
                        public void onClick(View view) {

                            alert.dismiss();

                            emailTxt.setText("");
                            subjectTxt.setText("");
                            messageTxt.setText("");
                        }
                    });


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

                    if(emailTxt.getText().length() > 0  && !Patterns.EMAIL_ADDRESS.matcher(emailTxt.getText().toString().trim()).matches() ){
                        if(!emailTxtLayout.isErrorEnabled()){
                            emailTxtLayout.setErrorEnabled(true);
                            emailTxtLayout.setHelperTextEnabled(true);
                            emailTxtLayout.setHelperText("Invalid email.");
                        }

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
