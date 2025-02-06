<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTraineeCourseAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // This checks if the trainee's active group has access to the course, or else, typing the URL would allow access even if the link is hidden

        $user = $request->user();

        if ($user->hasRole('Trainee')) {

            if ($request->route('course')->slug === 'kmh') {
                return $next($request);
            }

            if (!$user->trainee->active) {
                return redirect('dashboard');
            }
            
            $course = $request->route('course');
            $latestActiveGroup = $user->trainee->latestActiveGroup->first();
        
            if ($latestActiveGroup && !$latestActiveGroup->courses->contains('id', $course->id)) {
                return redirect('dashboard');
            }
        }
        
        return $next($request);
    }
}
