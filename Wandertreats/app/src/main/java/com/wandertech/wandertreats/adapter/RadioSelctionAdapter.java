package com.wandertech.wandertreats.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RadioButton;

import com.wandertech.wandertreats.MyApp;
import com.wandertech.wandertreats.R;
import com.wandertech.wandertreats.general.GeneralFunctions;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;

import androidx.appcompat.widget.AppCompatTextView;
import androidx.cardview.widget.CardView;
import androidx.core.content.ContextCompat;
import androidx.recyclerview.widget.RecyclerView;

public class RadioSelctionAdapter extends RecyclerView.Adapter<RadioSelctionAdapter.ViewHolder> {

    Context mContext;
    private RadioSelctionAdapter.ItemOnClickListener itemOnClickListener;
    ArrayList<HashMap<String, String>> dataList;
    private JSONArray datajsonArr;
    PlacesAdapter.setRecentLocClickList locClickList;
    private GeneralFunctions appFunctions;
    View view;

    public RadioSelctionAdapter(Context context, ArrayList<HashMap<String, String>> dataList) {
        this.mContext = context;
        this.dataList = dataList;
        this.appFunctions = MyApp.getInstance().getGeneralFun(context);

    }

    @Override
    public RadioSelctionAdapter.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        View view = LayoutInflater.from(mContext).inflate(R.layout.item_card_selection, parent, false);
        return new RadioSelctionAdapter.ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(RadioSelctionAdapter.ViewHolder holder, @SuppressLint("RecyclerView") final int position) {

        try{

            holder.selectedBtn.setChecked(false);
            holder.viewBox.setBackgroundResource(R.drawable.button_white_style);


            HashMap<String, String> map = dataList.get(position);

            holder.titleTxt.setText(map.get("title"));
            holder.messageTxt.setText(map.get("message"));

            if(dataList.get(position).get("selected").equalsIgnoreCase("Yes")){
                holder.selectedBtn.setChecked(true);
                holder.selectedBtn.setSelected(true);
                holder.viewBox.setBackgroundResource(R.drawable.button_border_style);
            }else{
                holder.selectedBtn.setChecked(false);
                holder.viewBox.setBackgroundResource(R.drawable.button_white_style);
                holder.selectedBtn.setSelected(false);
            }

            holder. viewBox.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    if (itemOnClickListener != null) {
                        itemOnClickListener.setOnItemClick(position);
                    }
                }
            });


            try{

                JSONObject object =  appFunctions.getJsonObject(map.get("data"));
                if(appFunctions.getJsonValue("eStatus",object.toString()).equalsIgnoreCase("Disable")){
                    holder.itemCard.setAlpha(0.5f);
                    holder.viewBox.setClickable(false);
                    holder.selectedBtn.setEnabled(false);
                }
            }catch (Exception e){

            }


        }catch (Exception e){
            appFunctions.showMessage(e.toString());
        }



    }

    @Override
    public int getItemCount() {
        return dataList.size();
    }

    public class ViewHolder extends RecyclerView.ViewHolder {

        AppCompatTextView messageTxt;
        AppCompatTextView titleTxt;
        LinearLayout viewBox;
        CardView itemCard;
        RadioButton selectedBtn;

        public ViewHolder(View itemView) {
            super(itemView);

            messageTxt = (AppCompatTextView) itemView.findViewById(R.id.mesageText);
            titleTxt = (AppCompatTextView) itemView.findViewById(R.id.titleText);
            itemCard = (CardView) itemView.findViewById(R.id.itemCard);
            viewBox = (LinearLayout) itemView.findViewById(R.id.viewBox);
            selectedBtn = (RadioButton) itemView.findViewById(R.id.selectedBtn);
        }
    }

    public interface ItemOnClickListener {
        void setOnItemClick(int position);
    }

    public void setOnItemClick(RadioSelctionAdapter.ItemOnClickListener onItemClick) {
        this.itemOnClickListener = onItemClick;
    }

}
