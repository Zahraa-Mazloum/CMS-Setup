import { Model } from "@/modules/form/lib/model";
import { Attrs } from "./attrs";
import { Field } from "@/modules/form/lib/field";
import { DataList } from "@/modules/form/types";
import { StringRule } from "@/modules/form/rules/string";
import { RequiredRule } from "@/modules/form/rules/required";

export class ContentLocal extends Model {
  type: string = "default";

  get id(): Field {
    return this.field("id", "ID", this.initData?.id ?? null);
  }

  get contentId(): Field {
    return this.field(
      "content_id",
      "Content ID",
      this.initData?.content_id ?? null
    );
  }

  get language(): Field {
    return this.field("language", "Language", this.initData?.language ?? "en");
  }

  get title(): Field {
    return this.field("title", "Title", this.initData?.title ?? "");
  }

  get excerpt(): Field {
    return this.field("excerpt", "Excerpt", this.initData?.excerpt ?? "");
  }

  get blocks(): Field {
    return this.field("blocks", "Text", this.initData?.blocks ?? {});
  }

  get attrs(): Attrs {
    return this.hasOne("attrs", (initData: DataList) => {
      return Attrs.generate(this.type, "local", initData);
    });
  }

  get rules(): any[] {
    return [
      [[this.title], StringRule],
      [[this.title], RequiredRule],
    ];
  }
}
