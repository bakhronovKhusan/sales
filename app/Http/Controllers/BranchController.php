<?php

namespace App\Http\Controllers;
use App\Components\Helper;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function get_all_branches_with_group_count(Request $request, Branch $branch)
    {
        $branch = $branch->newQuery();

        $subdomain = Helper::getSubdomain($request->headers->get('referer'));

        if ($subdomain) {
            $branch->whereHas('company', function ($q) use ($subdomain) {
                $q->where('subdomain', 'test');
            });
        } else {
            $company_id = (isset($request->company_id)) ? $request->company_id : 1;
            $branch->where('company_id', $company_id);
        }

        $branch = $branch->whereNotIn('id', [31, 31])->get();

        return $branch;
    }
}
