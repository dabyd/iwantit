@php
$canEdit = \App\Helpers\ProjectPermissionHelper::canEdit(auth()->user(), $project);
@endphp
<x-layouts.edit
	:controller="$controller"
	:data="$project"
	:video="$video ?? null"
	:video_fps="$video_fps ?? null"
	:video_w="$video_w ?? null"
	:video_h="$video_h ?? null"
	:distance_frames="$distance_frames ?? 0"
	:hotpointEditor="1"
	:hotpoints="$hotpoints ?? null"
	:productos="$productos ?? null"
	:related="$terr ?? null"
	:txtrelated="'territory'"
	:keylist="$kf ?? null"
	:ubp="$ubp ?? null"
	:datision="$datision ?? null"
	:tabs="'si'"
	:ai_url="$ai_url ?? null"
	:threshold_secs="$threshold_secs ?? null"
	:ia_clases="$ia_clases ?? null"
	:objects="$objects ?? null"
	:readonly="!$canEdit"
/>