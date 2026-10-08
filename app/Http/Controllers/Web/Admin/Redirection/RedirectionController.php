<?php

namespace App\Http\Controllers\Web\Admin\Redirection;

use App\Algorithms\Redirection\RedirectionAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Redirection\RedirectionRequest;
use App\Models\Redirection\Redirection;
use App\Parser\Redirection\RedirectionParser;
use Illuminate\Http\Request;

class RedirectionController extends Controller
{
    public function index(Request $request)
    {
        $datas = Redirection::query()
            ->filter($request)
            ->paginate(
                $request->input('perPage', 20)
            );

        return success(RedirectionParser::briefs($datas), pagination: pagination($datas));
    }

    public function show($id)
    {
        $redirection = Redirection::find($id);

        if (!$redirection) {
            errRedirectionGet();
        }

        return success(
            RedirectionParser::first($redirection)
        );
    }

    public function store(RedirectionRequest $request)
    {
        return (new RedirectionAlgo())
            ->create($request);
    }

    public function update(RedirectionRequest $request,$id) {
        return (new RedirectionAlgo($id))
            ->update($request);
    }

    public function destroy($id)
    {
        return (new RedirectionAlgo($id))
            ->delete();
    }
}
