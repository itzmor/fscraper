package il.co.itzikm.repeatingalarm;

import android.app.AlarmManager;
import android.app.AlertDialog;
import android.app.PendingIntent;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.content.pm.Signature;
import android.graphics.Color;
import android.os.Bundle;

import android.util.Base64;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Switch;
import android.widget.TextView;

import il.co.itzikm.common.activities.SampleActivityBase;
import il.co.itzikm.common.logger.Log;
import il.co.itzikm.common.logger.LogFragment;
import il.co.itzikm.common.logger.LogWrapper;
import il.co.itzikm.common.logger.MessageOnlyLogFilter;
import com.facebook.AccessToken;
import com.facebook.AccessTokenTracker;
import com.facebook.CallbackManager;
import com.facebook.FacebookCallback;
import com.facebook.FacebookException;
import com.facebook.login.LoginResult;
import com.facebook.login.widget.LoginButton;

import org.json.JSONException;
import org.json.JSONObject;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;

import java.util.Arrays;
import java.util.Calendar;

public class MainActivity extends SampleActivityBase {

    public static final String TAG = "MainActivity";

    final Context context = this;
    AlarmManager alarm_manager;
    PendingIntent pendingIntent;
    CallbackManager callbackManager;
    com.facebook.login.widget.LoginButton loginButton;
    public static AccessToken accessToken;
    public static String userId;
    Facebook facebook;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        stop_schedule();


        //
        final String EMAIL = "email";
        final String FRIENDS = "user_friends";

        callbackManager = CallbackManager.Factory.create();

        loginButton = (LoginButton) findViewById(R.id.login_button);
        loginButton.setReadPermissions(Arrays.asList(EMAIL, FRIENDS));
        accessToken = AccessToken.getCurrentAccessToken();
        boolean isLoggedIn = accessToken != null;
//        boolean isExpired = accessToken.isExpired();

//        try {
//            PackageInfo info = getPackageManager().getPackageInfo(
//                    "il.co.itzikm.repeatingalarm",
//                    PackageManager.GET_SIGNATURES);
//            for (Signature signature : info.signatures) {
//                MessageDigest md = MessageDigest.getInstance("SHA");
//                md.update(signature.toByteArray());
//                Log.d("KeyHash:", Base64.encodeToString(md.digest(), Base64.DEFAULT));
//            }
//        } catch (PackageManager.NameNotFoundException e) {
//
//        } catch (NoSuchAlgorithmException e) {
//
//        }
//        System.exit(1);
        loginButton.registerCallback(callbackManager, new FacebookCallback<LoginResult>() {
            @Override
            public void onSuccess(LoginResult loginResult) {
                accessToken = AccessToken.getCurrentAccessToken();
                userId = accessToken.getUserId();
                onCreateWhenAccessTokenValid();
            }

            @Override
            public void onCancel() {
                int t = 1;
            }

            @Override
            public void onError(FacebookException exception) {
                int t = 1;
            }

        });

        if (accessToken != null)
        {
            onCreateWhenAccessTokenValid();
        }

        AccessTokenTracker accessTokenTracker = new AccessTokenTracker() {
            @Override
            protected void onCurrentAccessTokenChanged(AccessToken oldAccessToken,
                                                       AccessToken currentAccessToken) {
                if (currentAccessToken == null) {
                    Switch mySwitch = (Switch)findViewById(R.id.subscribe_button);
                    Button myUpdateButton = (Button) findViewById(R.id.update_button);
                    Button myGetUnfriendsButton = (Button) findViewById(R.id.get_unfriends_button);
                    Switch myScheduleSwitch = (Switch)findViewById(R.id.should_schedule_switch);
                    TextView myScheduletimeTextView = (TextView)findViewById(R.id.schedule_time_textview);
                    mySwitch.setEnabled(false);
                    myUpdateButton.setEnabled(false);
                    myGetUnfriendsButton.setEnabled(false);
                    myScheduleSwitch.setEnabled(false);
                    myScheduletimeTextView.setEnabled(false);
                }
            }
        };
    }

    private void onCreateWhenAccessTokenValid()
    {
        WumConnectWithWs wumConnectWithWs = new WumConnectWithWs();
        wumConnectWithWs.run_wus_action(WumActionType.ISSUB, accessToken);
        JSONObject retobj = wumConnectWithWs.get_retobj();
        String uf = "";
        Boolean is_schedule=false;
        String schedule_time = "";
        try{
            uf = retobj.get("retval").toString();
            String is_schedule_str = retobj.get("is_schedule").toString();
            if (is_schedule_str.equals("1")) {
                is_schedule = true;
            } else {
                is_schedule = false;
            }
            schedule_time = retobj.getString("schedule_time");
        }
        catch (JSONException je)
        {
            Log.e(TAG, je.getMessage());
        }
        Switch mySwitch = (Switch)findViewById(R.id.subscribe_button);
        Button myUpdateButton = (Button) findViewById(R.id.update_button);
        Button myGetUnfriendsButton = (Button) findViewById(R.id.get_unfriends_button);
        Switch myScheduleSwitch = (Switch)findViewById(R.id.should_schedule_switch);
        TextView myScheduletimeTextView = (TextView)findViewById(R.id.schedule_time_textview);

        mySwitch.setEnabled(true);
        if (uf == "true")
        {
            mySwitch.setChecked(true);
            myUpdateButton.setEnabled(true);
            myGetUnfriendsButton.setEnabled(true);
            myScheduleSwitch.setEnabled(true);
            if (is_schedule) {
                myScheduleSwitch.setChecked(true);
                myScheduletimeTextView.setEnabled(true);
                runSchedule(schedule_time);
            } else
            {
                myScheduleSwitch.setChecked(false);
                myScheduletimeTextView.setEnabled(false);
                stop_schedule();
            }
            myScheduletimeTextView.setText(schedule_time);
        } else
        {
            mySwitch.setChecked(false);
            myUpdateButton.setEnabled(false);
            myGetUnfriendsButton.setEnabled(false);
            myScheduleSwitch.setEnabled(false);
            myScheduletimeTextView.setEnabled(false);
        }
        Log.i(TAG, "App started " + WumActions.getDate());
        facebook = new Facebook(accessToken);
    }


    protected void onDestroy() {
        super.onDestroy();
        stop_schedule();
    }

    private void runSchedule(String schedule_time)
    {
        int hour = Integer.parseInt(schedule_time.substring(0,2));
        int minute = Integer.parseInt(schedule_time.substring(2,4));
        Calendar calendaram = Calendar.getInstance();
        calendaram.set(Calendar.HOUR_OF_DAY, hour);
        calendaram.set(Calendar.MINUTE, minute);
        calendaram.set(Calendar.SECOND, 00);
        if (hour > 11)
        {
            calendaram.set(Calendar.AM_PM,Calendar.PM);
        }
        else
        {
            calendaram.set(Calendar.AM_PM,Calendar.AM);
        }
        Intent intent = new Intent("il.co.itzikm.MY_TIMER");
        pendingIntent = PendingIntent.getBroadcast(this, 0, intent, 0);
        alarm_manager = (AlarmManager) getSystemService(ALARM_SERVICE);
        alarm_manager.setRepeating(AlarmManager.RTC, calendaram.getTimeInMillis(), AlarmManager.INTERVAL_FIFTEEN_MINUTES,
                pendingIntent);
    }

    public void stop_schedule()
    {
        if (alarm_manager!=null) {
            alarm_manager.cancel(pendingIntent);
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }

    /** Create a chain of targets that will receive log data */
    @Override
    public void initializeLogging() {
        // Wraps Android's native log framework.
        LogWrapper logWrapper = new LogWrapper();
        // Using Log, front-end to the logging chain, emulates android.util.log method signatures.
        Log.setLogNode(logWrapper);

        // Filter strips out everything except the message text.
        MessageOnlyLogFilter msgFilter = new MessageOnlyLogFilter();
        logWrapper.setNext(msgFilter);

        // On screen logging via a fragment with a TextView.
        LogFragment logFragment = (LogFragment) getSupportFragmentManager()
                .findFragmentById(R.id.log_fragment);
        msgFilter.setNext(logFragment.getLogView());
        logFragment.getLogView().setTextAppearance(this, R.style.Log);
        logFragment.getLogView().setBackgroundColor(Color.WHITE);
    }
    public void subscribe_function(View view) {
        WumConnectWithWs wumConnectWithWs = new WumConnectWithWs();
        Switch mySwitch = (Switch)findViewById(R.id.subscribe_button);
        Button myUpdateButton = (Button) findViewById(R.id.update_button);
        Button myGetUnfriendsButton = (Button) findViewById(R.id.get_unfriends_button);
        Switch myScheduleSwitch = (Switch)findViewById(R.id.should_schedule_switch);
        TextView myScheduletimeTextView = (TextView) findViewById(R.id.schedule_time_textview);
        if (mySwitch.isChecked())
        {
            wumConnectWithWs.run_wus_action(WumActionType.SUBSCRIBE, true, "0300", accessToken);
            myUpdateButton.setEnabled(true);
            myGetUnfriendsButton.setEnabled(true);
            myScheduleSwitch.setEnabled(true);
            myScheduletimeTextView.setEnabled(true);
        } else
        {
            stop_schedule();
            wumConnectWithWs.run_wus_action(WumActionType.UNSUBSCRIBE, accessToken);
            myUpdateButton.setEnabled(false);
            myGetUnfriendsButton.setEnabled(false);
            myScheduleSwitch.setEnabled(false);
            myScheduletimeTextView.setEnabled(false);
        }
    }


    public void update_function(View view) {
        WumConnectWithWs wumConnectWithWs = new WumConnectWithWs();
        wumConnectWithWs.run_wus_action(WumActionType.UPDATE, accessToken);
    }

    public void set_schedule_function(View view)
    {
        final Switch myScheduleSwitch = (Switch)findViewById(R.id.should_schedule_switch);
        final TextView myScheduletimeText = (TextView)findViewById(R.id.schedule_time_textview);
        if (myScheduleSwitch.isChecked())
        {
            myScheduletimeText.setEnabled(true);
            //
            // get prompts.xml view
            LayoutInflater li = LayoutInflater.from(context);
            View promptsView = li.inflate(R.layout.prompts, null);

            AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(
                    context);

            // set prompts.xml to alertdialog builder
            alertDialogBuilder.setView(promptsView);

            final EditText userInput = (EditText) promptsView
                    .findViewById(R.id.editTextDialogUserInput);

            // set dialog message
            alertDialogBuilder
                    .setCancelable(false)
                    .setPositiveButton("OK",
                            new DialogInterface.OnClickListener() {
                                public void onClick(DialogInterface dialog,int id) {
                                    // get user input and set it to result
                                    // edit text
                                    myScheduletimeText.setText(userInput.getText());
                                    WumConnectWithWs wumConnectWithWs = new WumConnectWithWs();
                                    wumConnectWithWs.run_wus_action(WumActionType.UPDATE_USER,
                                            myScheduleSwitch.isChecked(), myScheduletimeText.getText().toString(), accessToken);
                                    runSchedule(userInput.getText().toString());
                                }
                            })
                    .setNegativeButton("Cancel",
                            new DialogInterface.OnClickListener() {
                                public void onClick(DialogInterface dialog, int id) {
                                    dialog.cancel();
                                }
                            });

            // create alert dialog
            AlertDialog alertDialog = alertDialogBuilder.create();

            // show it
            alertDialog.show();
            //

        } else
        {
            myScheduletimeText.setEnabled(false);
            WumConnectWithWs wumConnectWithWs = new WumConnectWithWs();
            wumConnectWithWs.run_wus_action(WumActionType.UPDATE_USER,
                    myScheduleSwitch.isChecked(), myScheduletimeText.getText().toString(), accessToken);
            stop_schedule();
        }
    }
    public void get_unfriends_list_function(View view) {
        String notification = WumActions.get_unfriends(accessToken);

        if (!notification.isEmpty()) {
            WumActions.sendNotification(this.getBaseContext(), notification);
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        callbackManager.onActivityResult(requestCode, resultCode, data);
        super.onActivityResult(requestCode, resultCode, data);
    }
}
