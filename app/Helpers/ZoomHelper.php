<?php

namespace App\Helpers;

use App\ScheduleZoomMeeting;
use App\Settings;
use App\ZoomMeeting;
use Carbon\Carbon;


class ZoomHelper {

    public static function createMeetingForSchedule($schedule, $date)
    {
        $timezone = CommonHelper::getSchoolTimezone();
        $meetingStart = Carbon::createFromFormat('Y-m-d H:i:s', $date.' '.$schedule->start_time, $timezone)->setTimezone("UTC");
        $meetingEnd = Carbon::createFromFormat('Y-m-d H:i:s', $date.' '.$schedule->end_time, $timezone)->setTimezone("UTC");
        $meetingDuration = $meetingEnd->diffInMinutes($meetingStart);

        $zoomHostUser = $schedule->teacher->user->zoom_email;

        $zoomClient = new ZoomClient();
        // Get User Settings
        $res = $zoomClient->getUserSettings($zoomHostUser);
        if($res['status'] == 0) 
        {
            return [
                'status' => 0,
                'message' => $res['message']
            ];
        }

        $userSettings = $res['user_settings'];
        $usePMI = $userSettings['schedule_meeting']['use_pmi_for_scheduled_meetings'];
        $PMIPassword = $userSettings['schedule_meeting']['pmi_password'];

        $meeting_password = $usePMI ? $PMIPassword : rand(100000,999999);
        $meeting_settings = NULL;

        if($usePMI)
        {
            // Get User
            $res = $zoomClient->getUser($zoomHostUser);
            if($res['status'] == 0) 
            {
                return [
                    'status' => 0,
                    'message' => $res['message']
                ];
            }
            $zoomUser = $res['user'];
            $pmi = $zoomUser['pmi'];
            
            // Get PMI Settings (Personal Meeting Room Settings)
            $res = $zoomClient->getMeeting($pmi);
            if($res['status'] == 0) 
            {
                return [
                    'status' => 0,
                    'message' => $res['message']
                ];
            }
            $zoomMeeting = $res['meeting'];
            
            $zoomMeetingSeetings = $zoomMeeting['settings'];
            $meeting_settings = [
                'host_video' => $zoomMeetingSeetings['host_video'],
                'participant_video' => $zoomMeetingSeetings['participant_video'],
                'cn_meeting' => $zoomMeetingSeetings['cn_meeting'],
                'in_meeting' => $zoomMeetingSeetings['in_meeting'],
                'join_before_host' => $zoomMeetingSeetings['join_before_host'],
                'mute_upon_entry' => $zoomMeetingSeetings['mute_upon_entry'],
                'watermark' => $zoomMeetingSeetings['watermark'],
                'use_pmi' => true,
                'approval_type' => $zoomMeetingSeetings['approval_type'],
                'audio' => $zoomMeetingSeetings['audio'],
                'auto_recording' => $zoomMeetingSeetings['auto_recording'],
                'enforce_login' => $zoomMeetingSeetings['enforce_login'],
                'enforce_login_domains' => $zoomMeetingSeetings['enforce_login_domains'],
                'alternative_hosts' => $zoomMeetingSeetings['alternative_hosts'],
                'close_registration' => $zoomMeetingSeetings['close_registration'],
                'waiting_room' => $zoomMeetingSeetings['waiting_room'],
                'registrants_email_notification' => $zoomMeetingSeetings['registrants_email_notification'],
                'meeting_authentication' => $zoomMeetingSeetings['meeting_authentication'],
            ];
        }

        $body = [
            'topic' => $schedule->class->title,
            'type' => '2', // Scheduled Meeting
            'start_time' => $meetingStart->format('Y-m-d').'T'.$meetingStart->format('H:i:s').'Z', // Timestamp in UTC
            'duration' => $meetingDuration,
            'timezone' => $timezone, // Time of meeting will also be visible in this timezone if provided (in adition to zoom user's timezone)
            'password' => $meeting_password  
        ];

        if($meeting_settings) {
            $body['settings'] = $meeting_settings;
        }

        $res = $zoomClient->createMeeting($zoomHostUser, $body);
        if($res['status'] == 0) {
            return [
                'status' => 0,
                'message' => $res['message']
            ];
        }

        $meeting = $res['meeting'];
        $zoom_meeting_id = ZoomMeeting::create($meeting);

        $scheduleZoomMeeting = new ScheduleZoomMeeting();
        $scheduleZoomMeeting->schedule_id = $schedule->id;
        $scheduleZoomMeeting->date = $date;
        $scheduleZoomMeeting->zoom_meeting_id = $zoom_meeting_id;
        $scheduleZoomMeeting->save();

        return [
            'status' => 1,
            'schedule_zoom_meeting' => $scheduleZoomMeeting
        ];
    }

    public static function deleteZoomMeeting($zoom_meeting_id)
    {
        $zoomClient = new ZoomClient();
        $res = $zoomClient->deleteMeeting($zoom_meeting_id);
        
        if($res['status'] == 0 && $res['code'] != 3001) {
            return [
                'status' => 0,
                'message' => $res['message']
            ];
        }

        ZoomMeeting::where('id', $zoom_meeting_id)->delete();

        return [
            'status' => 1
        ];
    }

    public static function syncZoomMeeting($zoomMeeting)
    {
        $zoomClient = new ZoomClient();
        $res = $zoomClient->getMeeting($zoomMeeting->id);
        
        if($res['status'] == 0)
        {
            return [
                'status' => 0,
                'message' => $res['message']
            ];
        }
        
        $meeting = $res['meeting'];
        $zoomMeeting->syncFromZoom($meeting);

        return [
            'status' => 1,
            'zoom_meeting' => $zoomMeeting
        ];
    }
}

?>