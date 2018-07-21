package il.co.itzikm.repeatingalarm;

import il.co.itzikm.common.logger.Log;
import com.facebook.AccessToken;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.URL;
import java.nio.charset.StandardCharsets;
import java.util.Map;
import java.util.concurrent.ExecutionException;

public    class WumConnectWithWs {

    JSONObject retobj = null;
    Boolean is_schedule;
    String schedule_time;
    String fb_account;
    public static final String TAG = "WumConnectWithWs";
    Thread thrd1;

    public void run_wus_action(final WumActionType action, Boolean is_schedule, String schedule_time, String fb_account, AccessToken accessToken)
    {
        this.is_schedule = is_schedule;
        this.schedule_time = schedule_time;
        this.fb_account = fb_account;
        run_wus_action(action, accessToken);
    }

    public void run_wus_action(final WumActionType action, final AccessToken accessToken) {
        final JSONObject obj = new JSONObject();
        Facebook facebook = null;

        if (AccessToken.getCurrentAccessToken() != null) {
            try {
//            obj.put("userid", accessToken.getUserId());
                obj.put("userid", AccessToken.getCurrentAccessToken().getUserId());
            } catch (JSONException je) {
                Log.e(TAG, je.toString());
            }

            if (action == WumActionType.SUBSCRIBE || action == WumActionType.GET_UNFRIENDS_LIST ||
                    action == WumActionType.UPDATE || action == WumActionType.UPDATE_USER) {
                facebook = new Facebook(accessToken);
            }

            if (action == WumActionType.SUBSCRIBE || action == WumActionType.UPDATE || action == WumActionType.UPDATE_USER) {
                Map<String, String> detailsMap = null;
                JSONArray f_from_res = null;
                try {
                    detailsMap = facebook.getFacebookDetails();
                } catch (InterruptedException e) {
                    e.printStackTrace();
                } catch (ExecutionException e) {
                    e.printStackTrace();
                }
                try {
                    String[] details = null;
                    for (String fri : detailsMap.values()) {
                        details = fri.split(",");
                    }
                    obj.put("name", details[0]);
                    obj.put("email", details[1]);
                    obj.put("is_schedule", this.is_schedule);
                    obj.put("schedule_time", this.schedule_time);
                    obj.put("fb_account", this.fb_account);
                } catch (JSONException je) {
                    Log.e(TAG, je.toString());
                }
            }

            sendJsonToPhp(action, obj);
        }
        else {
            Log.i(TAG, "You should login to facebook!");
        }

    }

    private void sendJsonToPhp(final WumActionType action, final JSONObject obj)
    {
        thrd1 = new Thread(new Runnable() {
            public void run() {
                try {
                    //byte[] postData = obj.to.getBytes(StandardCharsets.UTF_8);//urlParameters.getBytes( StandardCharsets.UTF_8 );
                    //int    postDataLength = postData.length;
                    String request        = action.url();
                    URL url            = new URL( request );
                    HttpURLConnection conn= (HttpURLConnection) url.openConnection();
                    conn.setDoOutput( true );
                    //conn.setInstanceFollowRedirects( false );
                    conn.setRequestMethod( "POST" );
                    conn.setRequestProperty( "Content-Type", "application/json");
                    //conn.setRequestProperty( "charset", "utf-8");
                    //conn.setRequestProperty( "Content-Length", Integer.toString( postDataLength ));
                    conn.setUseCaches( false );
                    try {
                        OutputStreamWriter wr= new OutputStreamWriter(conn.getOutputStream());
                        wr.write(obj.toString());
//                    wr.write("AAAAAAAAAAAAA");
                        wr.flush();
                        //                  OutputStream os = conn.getOutputStream();
//                    os.write(obj.toString().getBytes("UTF-8"));
                        //                os.flush();
                        //              os.close();
                        String line;
                        BufferedReader reader = new BufferedReader(new InputStreamReader(conn.getInputStream(), StandardCharsets.UTF_8));
//                        BufferedReader reader = new BufferedReader(new InputStreamReader(conn.getInputStream()));

                        String returned_json_string = "";
                        while ((line = reader.readLine()) != null) {
                            System.out.println(line);
                            returned_json_string = returned_json_string + line;
                        }
                        wr.close();
                        reader.close();
                        try {
                            retobj = new JSONObject(returned_json_string);
                        }
                        catch (JSONException e) {
                            retobj = new JSONObject(returned_json_string.substring(3));
                        }
                    }
                    catch (Exception ex)
                    {
                        Log.e("File exception", ex.toString());
                    }

                    //
                } catch (IOException e) {
                    Log.e("Json Exception", e.toString());
                }
            }
        });
        thrd1.start();
    }

    JSONObject get_retobj()
    {
        while (!Thread.interrupted() && retobj == null && AccessToken.getCurrentAccessToken() != null) {
            try {
                Thread.sleep(100);
            } catch (InterruptedException e1) {
            }
        }

        return retobj;
    }
}
