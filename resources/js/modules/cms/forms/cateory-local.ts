import { Model } from "@/modules/form/lib/model";
import { Attrs } from "./attrs";
import { Field } from "@/modules/form/lib/field";
import { DataList } from "@/modules/form/types";
import { StringRule } from "@/modules/form/rules/string";
import { RequiredRule } from "@/modules/form/rules/required";

export class CategoryLocal extends Model {
  type: string = "default";

  get id(): Field {
    return this.field("id", "ID", this.initData?.id ?? null);
  }

  get categoryId(): Field {
    return this.field(
      "category_id",
      "Category ID",
      this.initData?.category_id ?? null
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

  get attrs(): Attrs {
    return this.hasOne("attrs", (initData: DataList) => {
      return Attrs.generate(this.type, "local", initData, "category");
    });
  }

  get rules(): any[] {
    return [
      [[this.title], StringRule],
      [[this.title], RequiredRule],
    ];
  }
}
